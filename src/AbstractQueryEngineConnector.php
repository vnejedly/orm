<?php
namespace Hooloovoo\ORM;

use Hooloovoo\Database\Database;
use Hooloovoo\Database\Query\QueryInterface;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\MysqlDataTypes;
use Hooloovoo\QueryEngine\DataSourceConnector\ConnectorInterface;
use Hooloovoo\QueryEngine\DataSourceConnector\Exception\FieldNameException;
use Hooloovoo\QueryEngine\Query\Param\FilterParam;
use Hooloovoo\QueryEngine\Query\Param\SorterParam;
use Hooloovoo\QueryEngine\Filter;
use Hooloovoo\QueryEngine\Query\Query;
use Hooloovoo\QueryEngine\Sorter;
use Hooloovoo\QueryEngine\Pager;

/**
 * Class QueryEngineConnector
 */
abstract class AbstractQueryEngineConnector implements ConnectorInterface
{
    const FIELD_MAPPING_NAME = 0;
    const FIELD_MAPPING_TYPE = 1;

    /** @var QueryInterface */
    protected $eqlQuery;

    /** @var string[] */
    protected $fieldMapping = [];

    /** @var array */
    protected $filterMapping = [
        Filter::TYPE_EQ => '=',
        Filter::TYPE_NE => '!=',
        Filter::TYPE_GT => '>',
        Filter::TYPE_GE => '>=',
        Filter::TYPE_LT => '<',
        Filter::TYPE_LE => '<=',
        Filter::TYPE_CT => 'LIKE',
        Filter::TYPE_NC => 'NOT LIKE',
        Filter::TYPE_SW => 'LIKE',
        Filter::TYPE_EW => 'LIKE',
        Filter::TYPE_NL => 'IS NULL',
        Filter::TYPE_NN => 'IS NOT NULL',
    ];

    /** @var array */
    protected $filterValueQuotes = [
        Filter::TYPE_CT => ['%', '%'],
        Filter::TYPE_NC => ['%', '%'],
        Filter::TYPE_SW => ['', '%'],
        Filter::TYPE_EW => ['%', ''],
    ];

    /** @var array */
    protected $sorterDirections = [
        Sorter::DIRECTION_ASC => 'ASC',
        Sorter::DIRECTION_DESC => 'DESC',
    ];

    /** @var int */
    protected $dbTypeParamMapping = [
        MysqlDataTypes::TINYINT => Database::PARAM_INT,
        MysqlDataTypes::INT => Database::PARAM_INT,
        MysqlDataTypes::SMALLINT => Database::PARAM_INT,
        MysqlDataTypes::MEDIUMINT => Database::PARAM_INT,
        MysqlDataTypes::BIGINT => Database::PARAM_INT,
        MysqlDataTypes::FLOAT => Database::PARAM_STR,
        MysqlDataTypes::DOUBLE => Database::PARAM_STR,
        MysqlDataTypes::DECIMAL => Database::PARAM_STR,
        MysqlDataTypes::DATE => Database::PARAM_STR,
        MysqlDataTypes::DATETIME => Database::PARAM_STR,
        MysqlDataTypes::TIMESTAMP => Database::PARAM_STR,
        MysqlDataTypes::TIME => Database::PARAM_STR,
        MysqlDataTypes::YEAR => Database::PARAM_STR,
        MysqlDataTypes::CHAR => Database::PARAM_STR,
        MysqlDataTypes::VARCHAR => Database::PARAM_STR,
        MysqlDataTypes::BLOB => Database::PARAM_LOB,
        MysqlDataTypes::TEXT => Database::PARAM_STR,
        MysqlDataTypes::TINYBLOB => Database::PARAM_LOB,
        MysqlDataTypes::TINYTEXT => Database::PARAM_STR,
        MysqlDataTypes::MEDIUMBLOB => Database::PARAM_LOB,
        MysqlDataTypes::MEDIUMTEXT => Database::PARAM_STR,
        MysqlDataTypes::LONGBLOB => Database::PARAM_LOB,
        MysqlDataTypes::LONGTEXT => Database::PARAM_STR,
        MysqlDataTypes::ENUM => Database::PARAM_STR,
        MysqlDataTypes::JSON => Database::PARAM_STR,
    ];

    /**
     * @param Query $query
     */
    public function applyQuery(Query $query)
    {
        $this->applyFilter($query->getFilter());
        $this->applySorter($query->getSorter());
    }

    /**
     * @param Query $query
     */
    public function applyPager(Query $query)
    {
        $this->eqlQuery->append('LIMIT :limit OFFSET :offset');
        $this->eqlQuery->addParam('limit', $query->getPager()->getLimit(), Database::PARAM_INT);
        $this->eqlQuery->addParam('offset', $query->getPager()->getOffset(), Database::PARAM_INT);
    }

    /**
     * @param string $queryFieldAlias
     * @param string $dataSourceFieldName
     * @param int $type
     */
    public function mapField(string $queryFieldAlias, string $dataSourceFieldName, int $type)
    {
        $this->fieldMapping[$queryFieldAlias] = [
            self::FIELD_MAPPING_NAME => $dataSourceFieldName,
            self::FIELD_MAPPING_TYPE => $type,
        ];
    }

    /**
     * Initializes default field mapping
     */
    abstract protected function initDefaultFieldMapping();

    /**
     * @param Filter $filter
     * @throws FieldNameException
     */
    protected function applyFilter(Filter $filter)
    {
        $conditions = [];

        foreach ($filter->getParams() as $param) {
            $conditions[] = $this->handleFilterParam($param);
        }

        if (count($conditions) != 0) {
            $this->eqlQuery->append(' ' . implode(' AND ', $conditions));
        }
    }

    /**
     * @param Sorter $sorter
     * @throws FieldNameException
     */
    protected function applySorter(Sorter $sorter)
    {
        $columnDirections = [];

        foreach ($sorter->getParams() as $param) {
            $columnDirections[] = $this->handleSorterParam($param);
        }

        if (count($columnDirections) != 0) {
            $this->eqlQuery->append('ORDER BY ' . implode(', ', $columnDirections));
        }
    }

    /**
     * @param FilterParam $param
     * @return string
     */
    protected function handleFilterParam(FilterParam $param) : string
    {
        $paramName = $param->getName();

        $fieldName = $this->getFieldName($paramName);
        $dataType = $this->getFieldType($paramName);

        $operator = $this->filterMapping[$param->getType()];
        $placeholder = $this->sanitizeParamName($param->getName());
        $value = $this->quoteFilterValue($param->getValue(), $param->getType());

        if (is_null($param->getValue())) {
            return "{$fieldName} {$operator}";
        }

        $this->eqlQuery->addParam($placeholder, $value, $dataType);
        return "{$fieldName} {$operator} :{$placeholder}";

    }

    /**
     * @param SorterParam $param
     * @return string
     */
    protected function handleSorterParam(SorterParam $param)
    {
        $fieldName = $this->getFieldName($param->getName());
        $direction = $this->sorterDirections[$param->getDirection()];

        return "$fieldName $direction";
    }

    /**
     * @param string $paramName
     * @return string
     */
    protected function sanitizeParamName(string $paramName) : string
    {
        return str_replace('.', '_', $paramName);
    }

    /**
     * @param string $queryFieldAlias
     * @return string
     * @throws FieldNameException
     */
    protected function getFieldName(string $queryFieldAlias) : string
    {
        if (!array_key_exists($queryFieldAlias, $this->fieldMapping)) {
            throw new FieldNameException("Unknown field $queryFieldAlias");
        }

        return $this->fieldMapping[$queryFieldAlias][self::FIELD_MAPPING_NAME];
    }

    /**
     * @param string $queryFieldAlias
     * @return int
     * @throws FieldNameException
     */
    protected function getFieldType(string $queryFieldAlias) : int
    {
        if (!array_key_exists($queryFieldAlias, $this->fieldMapping)) {
            throw new FieldNameException("Unknown field $queryFieldAlias");
        }

        return $this->fieldMapping[$queryFieldAlias][self::FIELD_MAPPING_TYPE];
    }

    /**
     * @param mixed $value
     * @param int $type
     * @return mixed
     */
    protected function quoteFilterValue($value, int $type)
    {
        if (!array_key_exists($type, $this->filterValueQuotes)) {
            return $value;
        }

        $quotes = $this->filterValueQuotes[$type];
        return $quotes[0] . $value . $quotes[1];
    }

    /**
     * @param string $dbType
     * @return int
     */
    protected function getParamType(string $dbType) : int
    {
        return $this->dbTypeParamMapping[strtoupper($dbType)];
    }
}