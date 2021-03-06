<?php
namespace Hooloovoo\ORM\Relation\Manager;

use Hooloovoo\Database\Database;
use Hooloovoo\Database\Helper\TableLock;
use Hooloovoo\DatabaseMapping\Table;
use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\ComponentManagerInterface;
use Hooloovoo\ORM\EntityManager\EntityManagerInterface;
use Hooloovoo\ORM\Exception\EntityNotFoundException;
use Hooloovoo\ORM\Exception\LogicException;
use Hooloovoo\ORM\Relation\EQLQuery\EQLQuery;
use Hooloovoo\ORM\Relation\EQLQuery\EQLQueryInterface;
use Hooloovoo\ORM\Relation\EQLQuery\QueryEngineConnector;
use Hooloovoo\ORM\Relation\GroupedArray;
use Hooloovoo\ORM\Utils\ArrayPager;
use Hooloovoo\ORM\Utils\DataSetHelper;
use Hooloovoo\QueryEngine\Query\Query;
use Hooloovoo\QueryEngine\Sorter;

/**
 * Class AbstractManager
 */
abstract class AbstractManager implements ManagerInterface
{
    const SQL_MAX_PLACEHOLDERS = 1000;

    /** @var Database */
    protected $_database;

    /** @var EntityManagerInterface */
    protected $_parentManager;

    /** @var ComponentManagerInterface[] */
    protected $_componentManagers = [];

    /** @var EntityManagerInterface[] */
    protected $_persistenceManagers = [];

    /** @var ManagerInterface[] */
    protected $_relationManagers = [];

    /**
     * @return Table
     */
    public function getTableMapping(): Table
    {
        return $this->getParentManager()->getTableMapping();
    }

    /**
     * @return string
     */
    public function getEntityName() : string
    {
        return $this->getTableMapping()->getEntityName();
    }

    /**
     * @return EntityManagerInterface
     */
    public function getParentManager() : EntityManagerInterface
    {
        if (!$this->_parentManager instanceof EntityManagerInterface) {
            throw new LogicException("Parent manager not set");
        }

        return $this->_parentManager;
    }

    /**
     * @param string $tableName
     * @return ComponentManagerInterface
     */
    public function getComponentManager(string $tableName) : ComponentManagerInterface
    {
        if (!array_key_exists($tableName, $this->_componentManagers)) {
            throw new LogicException("Persistence manager for table $tableName not present");
        }

        return $this->_componentManagers[$tableName];
    }

    /**
     * @param string $queryString
     * @return EQLQueryInterface
     */
    public function getEQLQuery(string $queryString = '') : EQLQueryInterface
    {
        $query = new EQLQuery($queryString);

        foreach ($this->_persistenceManagers as $persistenceManager) {
            $query->addComponentParentTable($persistenceManager->getTableMapping());
            $query->addComponentTable(
                $persistenceManager->getTableMapping(),
                $this->isParentManager($persistenceManager)
            );
        }

        foreach ($this->_relationManagers as $relationManager) {
            $query->addComponentParentTable($relationManager->getTableMapping());
            $query->addComponentEQLQuery($relationManager->getEQLQuery(''));
        }

        return $query;
    }

    /**
     * @param bool $write
     * @return TableLock
     */
    public function lockTables(bool $write) : TableLock
    {
        $lock = $this->_database->createLock();
        foreach ($this->_componentManagers as $componentManager) {
            if ($componentManager instanceof self) {
                $subLock = $componentManager->lockTables($write);
                $lock->mergeLock($subLock);
            } else {
                $tableName = $componentManager->getTableMapping()->getName();
                $lock->addTable($tableName, $write);
            }
        }

        return $lock;
    }

    /**
     * @param EQLQueryInterface $query
     * @return array
     */
    public function query(EQLQueryInterface $query) : array
    {
        return $this->_parentManager->getDatabase()->getConnectionSlave()->execute($query)->fetchAll(true);
    }

    /**
     * @param int $primaryKey
     * @return mixed
     */
    protected function _getByPrimaryKey(int $primaryKey)
    {
        $collection = $this->_getByPrimaryKeys([$primaryKey]);

        if (count($collection) < 1) {
            throw new EntityNotFoundException($this->_parentManager->getTableMapping()->getEntityName());
        }

        return array_shift($collection);
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
            $primaryKeysSubSet = array_slice($primaryKeys, $offset, $limit);

            $condition = $this->getEQLQuery('WHERE {@.#} IN (:primaryKeys)');
            $condition->addMultiParam('primaryKeys', $primaryKeysSubSet, Database::PARAM_INT);

            $subCollectionUnsorted = $this->_getByCondition($condition);

            foreach ($primaryKeysSubSet as $primaryKey) {
                if (!array_key_exists($primaryKey, $subCollectionUnsorted)) {
                    throw new EntityNotFoundException($this->getTableMapping()->getEntityName());
                }

                $collection[$primaryKey] = $subCollectionUnsorted[$primaryKey];
            }

            $offset = $offset + $limit;
        }

        return $collection;
    }

    /**
     * @param EQLQueryInterface $condition
     * @return mixed
     */
    protected function _getSingleByCondition(EQLQueryInterface $condition)
    {
        $collection = $this->_getByCondition($condition);

        if (count($collection) < 1) {
            throw new EntityNotFoundException($this->_parentManager->getTableMapping()->getEntityName());
        }

        return array_shift($collection);
    }

    /**
     * @param EQLQueryInterface $condition
     * @return mixed[]
     */
    protected function _getByCondition(EQLQueryInterface $condition) : array
    {
        $query = $this->getBasicCompositeSelect('{*.$}');
        $query->append($condition->getQueryString());

        foreach ($condition->getParams() as $name => $param) {
            $query->addParam($name, $param->getValue(), $param->getType());
        }

        $resultSet = $this->query($query);
        $dataSetHelper = new DataSetHelper($resultSet);

        $collections = [];

        foreach ($this->_persistenceManagers as $componentManager) {
            $tableName = $componentManager->getTableMapping()->getName();
            $collections[$tableName] = $componentManager->getCollectionFromPrefixedResultSet($resultSet);
        }

        foreach ($this->_relationManagers as $componentManager) {
            $tableName = $componentManager->getTableMapping()->getName();
            $primaryKeyName = $componentManager->getTableMapping()->getSimplePrimaryKey()->getColumnName();
            $prefixedPKName = "$tableName.$primaryKeyName";

            $collections[$tableName] = $componentManager->getByPrimaryKeys(
                $dataSetHelper->getColumnValues($prefixedPKName, true, true)
            );
        }

        return $this->composeEntities($collections, $resultSet);
    }

    /**
     * @param Query $query
     * @param QueryEngineConnector $queryEngineConnector
     * @param int $totalCount
     * @return array
     */
    protected function _getByQueryEngine(
        Query $query,
        QueryEngineConnector $queryEngineConnector,
        int &$totalCount = null
    ) : array
    {
        $queryEngineConnector->applyQuery($query);
        $totalCount = $this->_getCountByCondition($queryEngineConnector->getEQLQuery());

        $queryEngineConnector->applyPager($query);
        $primaryKeys = $this->_getPrimaryKeysByCondition($queryEngineConnector->getEQLQuery());

        return $this->_getByPrimaryKeys($primaryKeys);
    }

    /**
     * @param EQLQueryInterface $condition
     * @return mixed[]
     */
    protected function _getPrimaryKeysByCondition(EQLQueryInterface $condition) : array
    {
        $query = $this->getXToOneCompositeSelect('{@.#}');
        $query->append($condition->getQueryString());

        foreach ($condition->getParams() as $name => $param) {
            $query->addParam($name, $param->getValue(), $param->getType());
        }

        $resultSet = $this->query($query);
        $dataSetHelper = new DataSetHelper($resultSet);

        return $dataSetHelper->getColumnValues($this->getTableMapping()->getSimplePrimaryKey()->getColumnName());
    }

    /**
     * @param EQLQueryInterface $condition
     * @return int
     */
    protected function _getCountByCondition(EQLQueryInterface $condition) : int
    {
        $query = $this->getXToOneCompositeSelect('count(DISTINCT {@.#}) AS cnt');
        $query->append($condition->getQueryString());

        foreach ($condition->getParams() as $name => $param) {
            $query->addParam($name, $param->getValue(), $param->getType());
        }

        return (int) $this->_parentManager->getDatabase()->getConnectionSlave()->execute($query)->fetchOne()['cnt'];
    }

    /**
     * @param GroupedArray $groupedArray
     * @return mixed
     */
    abstract protected function getEntityFromGroupedArray(GroupedArray $groupedArray);

    /**
     * @param string $selection
     * @return EQLQueryInterface
     */
    abstract protected function getBasicCompositeSelect(string $selection) : EQLQueryInterface;

    /**
     * @param string $selection
     * @return EQLQueryInterface
     */
    abstract protected function getXToOneCompositeSelect(string $selection): EQLQueryInterface;

    /**
     * @param Database $database
     */
    protected function setDatabase(Database $database)
    {
        $this->_database = $database;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return bool
     */
    protected function isParentManager(EntityManagerInterface $entityManager) : bool
    {
        return ($this->_parentManager->getTableMapping()->getName() == $entityManager->getTableMapping()->getName());
    }

    /**
     * @param EntityManagerInterface $persistenceManager
     * @param bool $isParent
     */
    protected function addPersistenceManager(EntityManagerInterface $persistenceManager, bool $isParent = false)
    {
        $name = $persistenceManager->getTableMapping()->getName();

        $this->_componentManagers[$name] = $persistenceManager;
        $this->_persistenceManagers[$name] = $persistenceManager;

        if ($isParent) {
            $this->_parentManager = $persistenceManager;
        }
    }

    /**
     * @param ManagerInterface $relationManager
     */
    protected function addRelationManager(ManagerInterface $relationManager)
    {
        $name = $relationManager->getParentManager()->getTableMapping()->getName();

        $this->_componentManagers[$name] = $relationManager;
        $this->_relationManagers[$name] = $relationManager;
    }

    /**
     * @param DataObjectInterface[][] $collections
     * @param array $resultSet
     * @return DataObjectInterface[]
     */
    protected function composeEntities(array $collections, array $resultSet) : array
    {
        $groupedEntities = [];
        foreach ($resultSet as $resultRow) {
            $parentTableName = $this->_parentManager->getTableMapping()->getName();
            $parentPKName = $this->_parentManager->getTableMapping()->getSimplePrimaryKey()->getColumnName();
            $prefixedParentPKName = "$parentTableName.$parentPKName";
            $parentPrimaryKey = $resultRow[$prefixedParentPKName];

            foreach ($this->_componentManagers as $componentManager) {
                $tableName = $componentManager->getTableMapping()->getName();
                $pkName = $componentManager->getTableMapping()->getSimplePrimaryKey()->getColumnName();
                $prefixedPKName = "$tableName.$pkName";
                $primaryKey = $resultRow[$prefixedPKName];

                if (!is_null($parentPrimaryKey) && !is_null($primaryKey)) {
                    $groupedEntities[$parentPrimaryKey][$tableName][$primaryKey] = $collections[$tableName][$primaryKey];
                }
            }
        }

        $finalCollection = [];
        foreach ($groupedEntities as $primaryKey => $groupedArray) {
            $finalCollection[$primaryKey] = $this->getEntityFromGroupedArray(new GroupedArray(
                $groupedArray,
                $this->_componentManagers
            ));
        }

        return $finalCollection;
    }
}