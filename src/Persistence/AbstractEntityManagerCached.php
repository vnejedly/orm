<?php

namespace Hooloovoo\ORM\Persistence;

use Hooloovoo\Database\Database;
use Hooloovoo\DatabaseMapping\Descriptor\Table\TableInterface as TableDescriptor;
use Hooloovoo\DatabaseMapping\Table as TableMapping;
use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\Cache\CacheInterface;
use Hooloovoo\ORM\Cache\RegisterInterface;
use Hooloovoo\ORM\Exception\EntityNotFoundException;

/**
 * Class AbstractEntityManagerCached
 *
 *
 *
 * TODO: This class is a stub under construction !!!
 *
 *
 *
 */
abstract class AbstractEntityManagerCached
{
    /** @var Database */
    protected $_database;

    /** @var TableMapping */
    protected $_tableMapping;

    /** @var CacheInterface */
    protected $_cache;

    /** @var bool */
    protected $_cachingEngineOn = false;

    /**
     * @param string $conditionString
     * @return ConditionQuery
     */
    public function getConditionQuery(string $conditionString) : ConditionQuery
    {
        return new ConditionQuery($conditionString);
    }

    /**
     * @param TableDescriptor $tableDescriptor
     */
    protected function _resolveMapping(TableDescriptor $tableDescriptor)
    {
        $this->_tableMapping = new TableMapping($tableDescriptor);
    }

    /**
     * @param RegisterInterface $register
     */
    protected function _resolveCache(RegisterInterface $register)
    {
        $this->_cache = $register->getCache(get_called_class());
        $this->_cachingEngineOn = true;
    }

    /**
     * @param int[] $primaryKeys
     * @return mixed[]
     */
    protected function _getByPrimaryKeys(array $primaryKeys) : array
    {
        $pkName = $this->_tableMapping->getSimplePrimaryKey()->getColumnName();
        $dataObjects = $this->_cache->getMultiple($primaryKeys);

        $nonExistingKeys = [];
        foreach ($dataObjects as $key => $dataObject) {
            if (is_null($dataObject)) {
                $nonExistingKeys[] = $key;
            }
        }

        if (count($nonExistingKeys) > 0) {
            $condition = $this->getConditionQuery("
              {$pkName} IN (:primaryKeys)
            ");

            $condition->initQueryAll($this->_tableMapping);
            $condition->addMultiParam('primaryKeys', $nonExistingKeys, Database::PARAM_INT);
            $resultSet = $this->_database->getConnectionSlave()->execute($condition)->fetchAll();

            if (count($resultSet) != count($nonExistingKeys)) {
                throw new EntityNotFoundException($this->_tableMapping->getName());
            }

            $nonExistingObjects = [];
            foreach ($resultSet as $resultRow) {
                $primaryKey = $resultRow[$pkName];
                $object = $this->_getEntityFromArray($resultRow);
                $dataObjects[$primaryKey] = $object;
                $nonExistingObjects[$primaryKey] = $object;
            }

            $this->_cache->setMultiple($nonExistingObjects);
        }

        return $dataObjects;
    }

    /**
     * @param ConditionQuery $conditionQuery
     * @return mixed[]
     */
    protected function _getByCondition(ConditionQuery $conditionQuery) : array
    {
        if ($this->_cachingEngineOn) {
            return $this->_getByConditionCached($conditionQuery);
        }

        return $this->_getByConditionNoCache($conditionQuery);
    }

    /**
     * @param ConditionQuery $conditionQuery
     * @return mixed[]
     */
    protected function _getByConditionNoCache(ConditionQuery $conditionQuery) : array
    {
        $pkName = $this->_tableMapping->getSimplePrimaryKey()->getColumnName();
        $conditionQuery->initQueryAll($this->_tableMapping);
        $resultSet = $this->_database->getConnectionSlave()->execute($conditionQuery)->fetchAll();

        $dataObjects = [];
        foreach ($resultSet as $resultRow) {
            $primaryKey = $resultRow[$pkName];
            $dataObjects[$primaryKey] = $this->_getEntityFromArray($resultRow);
        }

        return $dataObjects;
    }

    /**
     * @param ConditionQuery $conditionQuery
     * @return mixed[]
     */
    protected function _getByConditionCached(ConditionQuery $conditionQuery) : array
    {
        $pkName = $this->_tableMapping->getSimplePrimaryKey()->getColumnName();
        $conditionQuery->initQueryIds($this->_tableMapping);
        $resultSet = $this->_database->getConnectionSlave()->execute($conditionQuery)->fetchAll();

        $primaryKeys = [];
        foreach ($resultSet as $resultRow) {
            $primaryKeys[] = $resultRow[$pkName];
        }

        return $this->_getByPrimaryKeys($primaryKeys);
    }

    /**
     * @param DataObjectInterface $dataObject
     * @param bool $returnObject
     * @return mixed
     */
    protected function _insert(DataObjectInterface $dataObject, bool $returnObject = true)
    {
        $columnNames = [];
        $placeHolders = [];
        $values = [];
        foreach ($this->_tableMapping->getColumns() as $column) {
            if ($column->getIsAutoIncrement()) {
                continue;
            }

            $columnName = $column->getColumnName();
            $fieldName = $column->getEntityFieldName();

            $columnNames[] = $columnName;
            $placeHolders[] = ":$columnName";
            $values[$columnName] = $dataObject->getField($fieldName)->getValue();
        }

        $implodedColumnNames = implode(', ', $columnNames);
        $implodedPlaceholders = implode(', ', $placeHolders);

        $query = $this->_database->createQuery("
            INSERT INTO {$this->_tableMapping->getName()} ($implodedColumnNames) VALUES ($implodedPlaceholders)
        ");

        foreach ($values as $columnName => $value) {
            $query->addParam($columnName, $value, Database::PARAM_STR);
        }

        $this->_database->getConnectionMaster()->execute($query);

        if ($returnObject) {
            return $this->_get($this->_database->getConnectionMaster()->getLastInsertedId());
        }
    }

    /**
     * @param array $data
     * @return DataObjectInterface
     */
    abstract protected function _getEntityFromArray(array $data) : DataObjectInterface ;
}