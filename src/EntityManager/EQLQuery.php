<?php
namespace Hooloovoo\ORM\EntityManager;

use Hooloovoo\Database\Query\Query as DatabaseQuery;
use Hooloovoo\DatabaseMapping\Table as TableMapping;

/**
 * Class EQLQuery
 */
class EQLQuery extends DatabaseQuery
{
    const CH_OPEN = '{';
    const CH_CLOSE = '}';

    const RESERVED_TABLE = self::CH_OPEN . '@' . self::CH_CLOSE;
    const RESERVED_ALL = self::CH_OPEN . '*' . self::CH_CLOSE;
    const RESERVED_COUNT = self::CH_OPEN . '#' . self::CH_CLOSE;

    /** @var TableMapping */
    protected $_tableMapping;

    /**
     * EQLQuery constructor.
     *
     * @param string $queryString
     * @param TableMapping $tableMapping
     */
    public function __construct(string $queryString, TableMapping $tableMapping)
    {
        parent::__construct($queryString);
        $this->_tableMapping = $tableMapping;
    }

    /**
     * @return TableMapping
     */
    public function getTableMapping() : TableMapping
    {
        return $this->_tableMapping;
    }

    /**
     * @return string[]
     */
    protected function getReservedPlaceholders() : array
    {
        return [
            self::RESERVED_TABLE,
            self::RESERVED_ALL,
            self::RESERVED_COUNT
        ];
    }

    /**
     * @return string[]
     */
    protected function getReservedReplacements() : array
    {
        return [
            $this->_tableMapping->getName(),
            implode(', ', $this->getQuotedColumnNames($this->_tableMapping)),
            "COUNT({$this->_tableMapping->getSimplePrimaryKey()->getColumnName()})"
        ];
    }

    /**
     * @param TableMapping $tableMapping
     * @return string[]
     */
    protected function getQuotedColumnNames(TableMapping $tableMapping) : array
    {
        $columnNames = $tableMapping->getColumnNames();
        $quotedColumnNames = [];

        foreach ($columnNames as $columnName) {
            $quotedColumnNames[] = "`$columnName`";
        }

        return $quotedColumnNames;
    }

    /**
     * @return string
     */
    protected function getReplacedQueryString()
    {
        $placeholders = $this->getReservedPlaceholders();
        $replacements = $this->getReservedReplacements();

        foreach ($this->_tableMapping->getColumns() as $column) {
            $placeholders[] = self::CH_OPEN . "{$column->getEntityFieldName()}" . self::CH_CLOSE;
            $replacements[] = $column->getColumnName();
        }

        return str_replace($placeholders, $replacements, $this->_queryString);
    }

    /**
     * @return string
     */
    protected function _getOriginalQueryString()
    {
        return $this->getReplacedQueryString();
    }
}