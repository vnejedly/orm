<?php
namespace Hooloovoo\ORM\EntityManager;

use Hooloovoo\ORM\AbstractQueryEngineConnector;

/**
 * Class QueryEngineConnector
 */
class QueryEngineConnector extends AbstractQueryEngineConnector
{
    /** @var EQLQuery */
    protected $eqlQuery;

    /**
     * QueryEngineConnector constructor.
     *
     * @param EQLQuery $eqlQuery
     * @param string $appendWith
     */
    public function __construct(EQLQuery $eqlQuery, string $appendWith = 'WHERE')
    {
        $this->eqlQuery = $eqlQuery;
        $this->appendWith = $appendWith;
        $this->initDefaultFieldMapping();
    }

    /**
     * Initializes default field mapping
     */
    protected function initDefaultFieldMapping()
    {
        $table = $this->eqlQuery->getTableMapping();
        foreach ($table->getColumns() as $column) {
            $fieldName = $column->getEntityFieldName();
            $this->mapField($fieldName, '{' . $fieldName . '}', $this->getParamType($column->getDataType()));
        }
    }
}