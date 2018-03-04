<?php
namespace Hooloovoo\ORM\Persistence;

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
     */
    public function __construct(EQLQuery $eqlQuery)
    {
        $this->eqlQuery = $eqlQuery;
        $this->initDefaultFieldMapping();
    }

    /**
     * @return EQLQuery
     */
    public function getEQLQuery() : EQLQuery
    {
        return $this->eqlQuery;
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