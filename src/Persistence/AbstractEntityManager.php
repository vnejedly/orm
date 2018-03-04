<?php
namespace Hooloovoo\ORM\Persistence;

use Hooloovoo\Database\Database;
use Hooloovoo\ORM\EventDispatcher\ConnectorInterface as DispatcherConnector;
use Hooloovoo\DatabaseMapping\Descriptor\Table\TableInterface as TableDescriptor;
use Hooloovoo\DatabaseMapping\Table as TableMapping;
use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\Exception\EntityNotFoundException;
use Hooloovoo\ORM\Exception\LogicException;
use Hooloovoo\ORM\Exception\NonOriginalEntityException;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\MysqlDataTypes;
use Hooloovoo\QueryEngine\Query\Query;

/**
 * Class AbstractEntityManager
 */
abstract class AbstractEntityManager implements EntityManagerInterface
{
    const SQL_MAX_PLACEHOLDERS = 1000;

    /** @var Database */
    protected $_database;

    /** @var TableMapping */
    protected $_tableMapping;

    /** @var DispatcherConnector */
    protected $_dispatcherConnector;

    /**
     * @param string $conditionString
     * @return EQLQuery
     */
    public function getEQLQuery(string $conditionString = '') : EQLQuery
    {
        return new EQLQuery($conditionString, $this->_tableMapping);
    }

    /**
     * @return TableMapping
     */
    public function getTableMapping() : TableMapping
    {
        return $this->_tableMapping;
    }

    /**
     * @return Database
     */
    public function getDatabase() : Database
    {
        return $this->_database;
    }

    /**
     * @param EQLQuery $query
     * @return int
     */
    public function getCount(EQLQuery $query) : int
    {
        $resultSet = $this->_database->getConnectionSlave()->execute($query)->fetchAll(false);
        return (int) $resultSet[0][0];
    }

    /**
     * @param int $primaryKey
     */
    public function delete(int $primaryKey)
    {
        $entity = $this->_getByPrimaryKey($primaryKey);
        $this->_dispatcherConnector->beforeDelete($this->getEventName(self::EVENT_PREFIX_DELETE_BEFORE), $this, $entity);

        $query = $this->_database->createQuery("
            DELETE FROM {$this->_tableMapping->getName()} 
            WHERE {$this->_tableMapping->getSimplePrimaryKey()->getColumnName()} = :primaryKey
        ");

        $query->addParam('primaryKey', $primaryKey, Database::PARAM_INT);
        $this->_database->getConnectionMaster()->execute($query);
        $this->_dispatcherConnector->afterDelete($this->getEventName(self::EVENT_PREFIX_DELETE_AFTER), $this, $entity);
    }

    /**
     * @param array $fieldValues
     * @param bool $returnObject
     * @return mixed
     */
    protected function _create(array $fieldValues, bool $returnObject = true)
    {
        $entity = $this->getNewEntity();

        foreach ($fieldValues as $fieldName => $value) {
            $entity->{$fieldName} = $value;
        }

        return $this->_createByEntity($entity, $returnObject);
    }

    /**
     * @param DataObjectInterface $dataObject
     * @param bool $returnObject
     * @return mixed
     */
    protected function _createByEntity(DataObjectInterface $dataObject, bool $returnObject = true)
    {
        $this->_dispatcherConnector->beforeCreate($this->getEventName(self::EVENT_PREFIX_CREATE_BEFORE), $this, $dataObject);

        $columnNames = [];
        $placeHolders = [];
        $data = []; /** @var DOFieldDBTypeMapping[] $data */

        foreach ($this->_tableMapping->getColumns() as $column) {
            if ($column->getIsAutoIncrement()) {
                continue;
            }

            $columnName = $column->getColumnName();
            $fieldName = $column->getEntityFieldName();

            $columnNames[] = "`$columnName`";
            $placeHolders[] = ":$columnName";

            $data[$columnName] = new DOFieldDBTypeMapping($dataObject->getField($fieldName));
        }

        $implodedColumnNames = implode(', ', $columnNames);
        $implodedPlaceholders = implode(', ', $placeHolders);

        $query = $this->_database->createQuery("
            INSERT INTO `{$this->_tableMapping->getName()}` ($implodedColumnNames) 
            VALUES ($implodedPlaceholders)
        ");

        foreach ($data as $columnName => $field) {
            $query->addParam($columnName, $field->getInsertValue(), $field->getInsertType());
        }

        $this->_database->getConnectionMaster()->execute($query);

        $primaryKeyColumn = $this->_tableMapping->getSimplePrimaryKey();
        if ($primaryKeyColumn->getIsAutoIncrement()) {
            $primaryKey = $this->_database->getConnectionMaster()->getLastInsertedId();
        } else {
            $primaryKey = $dataObject->getField($primaryKeyColumn->getEntityFieldName())->getValue();
        }

        $this->_dispatcherConnector->afterCreate($this->getEventName(self::EVENT_PREFIX_CREATE_AFTER), $this, $dataObject, $primaryKey);

        if ($returnObject) {
            return $this->_getByPrimaryKey($primaryKey);
        }
    }

    /**
     * @param int $primaryKey
     * @param DataObjectInterface $dataObject
     * @param bool $returnObject
     * @return mixed
     */
    protected function _replace(int $primaryKey, DataObjectInterface $dataObject, bool $returnObject = true)
    {
        $values = $dataObject->getSerialized();
        $primaryKeyFieldName = $this->_tableMapping->getSimplePrimaryKey()->getEntityFieldName();
        unset ($values[$primaryKeyFieldName]);

        return $this->_update($primaryKey, $values, $returnObject);
    }

    /**
     * @param int $primaryKey
     * @param array $fieldValues
     * @param bool $returnObject
     * @return mixed
     */
    protected function _update(int $primaryKey, array $fieldValues, bool $returnObject = true)
    {
        $entity = $this->_getByPrimaryKey($primaryKey); /** @var DataObjectInterface $entity */

        foreach ($fieldValues as $fieldName => $value) {
            $entity->{$fieldName} = $value;
        }

        return $this->_updateByEntity($entity, $returnObject);
    }

    /**
     * @param DataObjectInterface $entity
     * @param bool $returnObject
     * @return mixed
     */
    protected function _updateByEntity(DataObjectInterface $entity, bool $returnObject = true)
    {
        $this->_dispatcherConnector->beforeUpdate($this->getEventName(self::EVENT_PREFIX_UPDATE_BEFORE), $this, $entity);

        $parts = [];
        $data = []; /** @var DOFieldDBTypeMapping[] $data */
        $primaryKeyName = $this->_tableMapping->getSimplePrimaryKey()->getColumnName();
        $primaryKey = null;

        foreach ($this->_tableMapping->getEntityFieldNames() as $fieldName) {
            $field = $entity->getField($fieldName);
            $columnName = $this->_tableMapping->getColumnForField($fieldName);
            if ($columnName == $primaryKeyName) {
                if ($field->isUnlocked() || is_null($field->getValue())) {
                    throw new NonOriginalEntityException($this->_tableMapping->getEntityName());
                }

                $primaryKey = $field->getValue();
                $data['_primaryKey'] = new DOFieldDBTypeMapping($field);
            } elseif ($field->isUnlocked()) {
                $parts[] = "`$columnName` = :$fieldName";
                $data[$fieldName] = new DOFieldDBTypeMapping($field);
            }
        }

        if (count($parts) != 0) {
            $setString = implode(', ', $parts);

            $query = $this->_database->createQuery("
                UPDATE {$this->_tableMapping->getName()} SET $setString 
                WHERE {$this->_tableMapping->getSimplePrimaryKey()->getColumnName()} = :_primaryKey
            ");

            foreach ($data as $fieldName => $mapping) {
                $query->addParam($fieldName, $mapping->getInsertValue(), $mapping->getInsertType());
            }

            $this->_database->getConnectionMaster()->execute($query);
        }

        $this->_dispatcherConnector->afterUpdate($this->getEventName(self::EVENT_PREFIX_UPDATE_AFTER), $this, $entity);

        if ($returnObject) {
            return $this->_getByPrimaryKey($primaryKey);
        }
    }

    /**
     * @param int $primaryKey
     * @return mixed
     */
    protected function _getByPrimaryKey(int $primaryKey)
    {
        return $this->_getByPrimaryKeys([$primaryKey])[$primaryKey];
    }

    /**
     * @param int[] $primaryKeys
     * @return mixed[]
     */
    protected function _getByPrimaryKeys(array $primaryKeys) : array
    {
        $count = count($primaryKeys);

        if (0 == $count) {
            return [];
        }

        $offset = 0;
        $limit = self::SQL_MAX_PLACEHOLDERS;
        $collection = [];

        while ($offset < $count) {
            $query = $this->getEQLQuery("
                SELECT {*} FROM {@} 
                WHERE `{$this->_tableMapping->getSimplePrimaryKey()->getColumnName()}` IN (:primaryKeys)
            ");

            $query->addMultiParam(
                'primaryKeys',
                array_slice($primaryKeys, $offset, $limit),
                Database::PARAM_INT
            );

            foreach ($this->_getObjects($query) as $key => $value) {
                $collection[$key] = $value;
            }

            $offset = $offset + $limit;
        }

        if (count($collection) != count($primaryKeys)) {
            $missingKeys = implode(', ', array_diff($primaryKeys, array_keys($collection)));
            throw new EntityNotFoundException($missingKeys);
        }

        return $collection;
    }

    /**
     * @param EQLQuery $query
     * @return mixed[]
     */
    protected function _getObjects(EQLQuery $query) : array
    {
        $resultSet = $this->_database->getConnectionSlave()->execute($query)->fetchAll();
        return $this->_getCollectionFromResultSet($resultSet);
    }

    /**
     * @param EQLQuery $query
     * @return mixed
     */
    protected function _getObject(EQLQuery $query)
    {
        $collection = $this->_getObjects($query);

        if (count($collection) < 1) {
            throw new EntityNotFoundException($this->getTableMapping()->getEntityName());
        }

        return array_shift($collection);
    }

    /**
     * @param Query $query
     * @param QueryEngineConnector $queryEngineConnector
     * @return array
     */
    protected function _getByQueryEngine(Query $query, QueryEngineConnector $queryEngineConnector) : array
    {
        $queryEngineConnector->applyQuery($query);
        $queryEngineConnector->applyPager($query);

        return $this->_getObjects($queryEngineConnector->getEQLQuery());
    }

    /**
     * @param Query $query
     * @param QueryEngineConnector $queryEngineConnector
     * @return int
     */
    protected function _getCountByQueryEngine(Query $query, QueryEngineConnector $queryEngineConnector) : int
    {
        $queryEngineConnector->applyQuery($query);
        $eqlQuery = $queryEngineConnector->getEQLQuery();

        return (int) $this->getDatabase()->getConnectionSlave()->execute($eqlQuery)->fetchOne()['cnt'];
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @param int $type
     * @return mixed
     */
    protected function _getObjectByField(string $fieldName, $value, int $type = Database::PARAM_STR)
    {
        $query = $this->getEQLQuery('SELECT {*} FROM {@} WHERE {' . $fieldName . '} = :val LIMIT 1');
        $query->addParam('val', $value, $type);

        return $this->_getObject($query);
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @param int $type
     * @return mixed[]
     */
    protected function _getObjectsByField(string $fieldName, $value, int $type = Database::PARAM_STR) : array
    {
        $query = $this->getEQLQuery('SELECT {*} FROM {@} WHERE {' . $fieldName . '} = :val');
        $query->addParam('val', $value, $type);

        return $this->_getObjects($query);
    }

    /**
     * @param array $fieldSet
     * @return mixed
     */
    protected function _getObjectByFieldSet(array $fieldSet)
    {
        $collection = $this->_getObjectsByFieldSet($fieldSet);

        if (count($collection) < 1) {
            throw new EntityNotFoundException($this->getTableMapping()->getEntityName());
        }

        return array_shift($collection);
    }

    /**
     * @param array $fieldSet
     * @return mixed[]
     */
    protected function _getObjectsByFieldSet(array $fieldSet) : array
    {
        $subConditions = [];
        foreach ($fieldSet as $name => $field) {
            $columnName = $this->_tableMapping->getColumnForField($name);
            $type = strtoupper($this->_tableMapping->getColumn($columnName)->getDataType());

            $expression = '{' . $name . '}';
            if ($type == MysqlDataTypes::FLOAT) {
                $expression = "CAST($expression AS CHAR)";
            }

            $subConditions[] = "$expression = :$name";
        }

        if (count($fieldSet) > 0) {
            $condition = implode(' AND ', $subConditions);
        } else {
            $condition = 1;
        }

        $query = $this->getEQLQuery("SELECT {*} FROM {@} WHERE $condition");

        foreach ($fieldSet as $name => $field) {
            $query->addParam($name, $field[0], $field[1]);
        }

        return $this->_getObjects($query);
    }


    /**
     * @param array $resultSet
     * @return mixed[]
     */
    protected function _getCollectionFromResultSet(array $resultSet) : array
    {
        $primaryKeyName = $this->_tableMapping->getSimplePrimaryKey()->getColumnName();

        $dataObjects = [];
        foreach ($resultSet as $resultRow) {
            $primaryKey = $resultRow[$primaryKeyName];
            $dataObjects[$primaryKey] = $this->getEntityFromRow($resultRow);
        }

        return $dataObjects;
    }

    /**
     * @param array $resultSet
     * @return mixed[]
     */
    protected function _getCollectionFromPrefixedResultSet(array $resultSet) : array
    {
        $tableName = $this->_tableMapping->getName();
        $primaryKeyName = $this->_tableMapping->getSimplePrimaryKey()->getColumnName();
        $columnNames = $this->_tableMapping->getColumnNames();

        $dataObjects = [];
        foreach ($resultSet as $resultRow) {
            $unPrefixedRow = [];
            foreach ($columnNames as $columnName) {
                $prefixedColumnName = "$tableName.$columnName";
                if (!array_key_exists($prefixedColumnName, $resultRow)) {
                    throw new LogicException("Column $prefixedColumnName not present in result set");
                }

                $unPrefixedRow[$columnName] = $resultRow[$prefixedColumnName];
            }

            $primaryKey = $unPrefixedRow[$primaryKeyName];
            if (!is_null($primaryKey)) {
                $dataObjects[$primaryKey] = $this->getEntityFromRow($unPrefixedRow);
            }
        }

        return $dataObjects;
    }

    /**
     * @param string $eventPrefix
     * @return string
     */
    protected function getEventName(string $eventPrefix) : string
    {
        return $eventPrefix . '.' . strtolower($this->_tableMapping->getEntityName());
    }

    /**
     * @param Database $database
     */
    protected function setDatabase(Database $database)
    {
        $this->_database = $database;
    }

    /**
     * @param DispatcherConnector $dispatcherConnector
     */
    protected function setDispatcherConnector(DispatcherConnector $dispatcherConnector)
    {
        $this->_dispatcherConnector = $dispatcherConnector;
    }

    /**
     * @param TableDescriptor $tableDescriptor
     */
    protected function resolveMapping(TableDescriptor $tableDescriptor)
    {
        $this->_tableMapping = new TableMapping($tableDescriptor);
    }

    /**
     * @param array $data
     * @return DataObjectInterface
     */
    abstract protected function getEntityFromRow(array $data) : DataObjectInterface ;
}