<?php
namespace Hooloovoo\ORM\Relation\EQLQuery;

use Hooloovoo\Database\Query\Query;
use Hooloovoo\DatabaseMapping\Table;

/**
 * Class EQLQuery
 */
class EQLQuery extends Query implements EQLQueryInterface
{
    const CH_OPEN = '{';
    const CH_CLOSE = '}';

    const RESERVED_PARENT_SELECTION_PK = self::CH_OPEN . '#' . self::CH_CLOSE;
    const RESERVED_PARENT_VALUE_PK = self::CH_OPEN . '@.#' . self::CH_CLOSE;
    const RESERVED_ALL_TABLES_SMART = self::CH_OPEN . '*.$' . self::CH_CLOSE;
    const RESERVED_ALL_TABLES_PKS = self::CH_OPEN . '*.#' . self::CH_CLOSE;
    const RESERVED_ALL_TABLES_ALL_FIELDS = self::CH_OPEN . '*.' . self::RESERVED_ALL_FIELDS;
    const RESERVED_ALL_FIELDS = '*' . self::CH_CLOSE;
    const RESERVED_COUNT = self::CH_OPEN . '!' . self::CH_CLOSE;

    /** @var Table */
    protected $_parentComponentTable;

    /** @var EQLQueryInterface[] */
    protected $_componentEqlQueries = [];

    /** @var Table[] */
    protected $_componentTables = [];

    /** @var Table[]  */
    protected $_componentParentTables = [];

    /**
     * @return Table
     */
    public function getParentComponentTable() : Table
    {
        return $this->_parentComponentTable;
    }

    /**
     * @param Table $tableMapping
     * @param bool $parent
     * @param bool $isFromSubSelect
     */
    public function addComponentTable(Table $tableMapping, bool $parent = false, bool $isFromSubSelect = false)
    {
        $this->_componentTables[$tableMapping->getName()] = $tableMapping;

        if ($parent) {
            $this->_parentComponentTable = $tableMapping;
        }
    }

    /**
     * @param Table $tableMapping
     */
    public function addComponentParentTable(Table $tableMapping)
    {
        $this->_componentParentTables[] = $tableMapping;
    }

    /**
     * @param EQLQueryInterface $eqlQuery
     */
    public function addComponentEQLQuery(EQLQueryInterface $eqlQuery)
    {
        $this->_componentEqlQueries[$eqlQuery->getParentComponentTable()->getName()] = $eqlQuery;

        foreach ($eqlQuery->getComponentTables() as $table) {
            $this->addComponentTable($table);
        }
    }

    /**
     * @return Table[]
     */
    public function getComponentTables() : array
    {
        return $this->_componentTables;
    }

    /**
     * @return Table[]
     */
    public function getComponentParentTables() : array 
    {
        return $this->_componentParentTables;
    }

    /**
     * @param Table $componentTable
     * @return string
     */
    public function getAllTableFields(Table $componentTable) : string
    {
        $columns = [];
        foreach ($componentTable->getColumnNames() as $columnName) {
            $prefixedColumn = "`{$componentTable->getName()}`.`{$columnName}`";
            $columnAlias = "`{$componentTable->getName()}.{$columnName}`";
            $columns[] = "$prefixedColumn AS $columnAlias";
        }

        return implode(', ', $columns);
    }

    /**
     * @return string
     */
    public function getSelectionParentPrimaryKey() : string
    {
        $columnName = $this->_parentComponentTable->getSimplePrimaryKey()->getColumnName();
        $tableName = $this->_parentComponentTable->getName();
        $prefixedColumn = "`$tableName`.`$columnName`";
        $columnAlias = "`$tableName.$columnName`";
        return "$prefixedColumn AS $columnAlias";
    }

    /**
     * @return string
     */
    public function getValueParentPrimaryKey() : string
    {
        $columnName = $this->_parentComponentTable->getSimplePrimaryKey()->getColumnName();
        return "{$this->_parentComponentTable->getName()}.{$columnName}";
    }

    /**
     * @return string
     */
    public function getSelectionAllPrimaryKeys() : string
    {
        $columns = [];

        foreach ($this->_componentParentTables as $table) {
            $columnName = $table->getSimplePrimaryKey()->getColumnName();

            $prefixedColumn = "`{$table->getName()}`.`{$columnName}`";
            $columnAlias = "`{$table->getName()}.{$columnName}`";
            $columns[] = "$prefixedColumn AS $columnAlias";
        }

        return implode(', ', $columns);
    }

    /**
     * @return string
     */
    public function getSelectionSmartColumns() : string
    {
        $columns = [];

        foreach ($this->_componentParentTables as $table) {
            if (!$this->_isRelationalComponent($table)) {
                $columns[] = $this->getAllTableFields($table);
            } else {
                $columnName = $table->getSimplePrimaryKey()->getColumnName();

                $prefixedColumn = "`{$table->getName()}`.`{$columnName}`";
                $columnAlias = "`{$table->getName()}.{$columnName}`";
                $columns[] = "$prefixedColumn AS $columnAlias";
            }
        }

        return implode(', ', $columns);
    }

    /**
     * @return string
     */
    public function getAllSelectionColumns() : string
    {
        $columns = [];
        foreach ($this->_componentTables as $componentTable) {
            $columns[] = $this->getAllTableFields($componentTable);
        }

        return implode(', ', $columns);
    }

    /**
     * @param Table $table
     * @return bool
     */
    protected function _isRelationalComponent(Table $table) : bool
    {
        return array_key_exists($table->getName(), $this->_componentEqlQueries);
    }

    /**
     * @return string[]
     */
    protected function _getReservedPlaceholders() : array
    {
        return [
            self::RESERVED_PARENT_SELECTION_PK,
            self::RESERVED_PARENT_VALUE_PK,
            self::RESERVED_ALL_TABLES_SMART,
            self::RESERVED_ALL_TABLES_PKS,
            self::RESERVED_ALL_TABLES_ALL_FIELDS,
            self::RESERVED_COUNT
        ];
    }

    /**
     * @return string[]
     */
    protected function _getReservedReplacements() : array
    {
        return [
            $this->getSelectionParentPrimaryKey(),
            $this->getValueParentPrimaryKey(),
            $this->getSelectionSmartColumns(),
            $this->getSelectionAllPrimaryKeys(),
            $this->getAllSelectionColumns(),
            $this->_getCountSelection(),
        ];
    }

    /**
     * @return string
     */
    protected function _getCountSelection() : string
    {
        return "COUNT({$this->_parentComponentTable->getSimplePrimaryKey()->getColumnName()})";
    }

    /**
     * @return string
     */
    protected function _getReplacedQueryString()
    {
        $queryString = parent::_getOriginalQueryString();

        $placeholders = $this->_getReservedPlaceholders();
        $replacements = $this->_getReservedReplacements();
        $queryString = str_replace($placeholders, $replacements, $queryString);

        $placeholders = $replacements = [];
        foreach ($this->_componentTables as $componentTable) {
            $placeholders[] = self::CH_OPEN . "{$componentTable->getEntityName()}." . self::RESERVED_ALL_FIELDS;
            $replacements[] = $this->getAllTableFields($componentTable);
        }
        $queryString = str_replace($placeholders, $replacements, $queryString);

        $placeholders = $replacements = [];
        foreach ($this->_componentTables as $componentTable) {
            foreach ($componentTable->getColumns() as $column) {
                $placeholders[] = self::CH_OPEN . "{$componentTable->getEntityName()}.{$column->getEntityFieldName()}" . self::CH_CLOSE;
                $replacements[] = "{$componentTable->getName()}.{$column->getColumnName()}";
            }
        }
        $queryString = str_replace($placeholders, $replacements, $queryString);

        $placeholders = $replacements = [];
        foreach ($this->_componentTables as $componentTable) {
            $placeholders[] = self::CH_OPEN . "{$componentTable->getEntityName()}" . self::CH_CLOSE;
            $replacements[] = "`{$componentTable->getName()}`";
        }
        $queryString = str_replace($placeholders, $replacements, $queryString);

        return $queryString;
    }

    /**
     * @return string
     */
    protected function _getOriginalQueryString()
    {
        return $this->_getReplacedQueryString();
    }
}