<?php
namespace Hooloovoo\ORM\Relation\EQLQuery;

use Hooloovoo\ORM\AbstractQueryEngineConnector;

/**
 * Class QueryEngineConnector
 */
class QueryEngineConnector extends AbstractQueryEngineConnector
{
    /** @var EQLQueryInterface */
    protected $eqlQuery;

    /**
     * QueryEngineConnector constructor.
     *
     * @param EQLQueryInterface $eqlQuery
     */
    public function __construct(EQLQueryInterface $eqlQuery)
    {
        $this->eqlQuery = $eqlQuery;
        $this->initDefaultFieldMapping();
    }

    /**
     * @return EQLQueryInterface
     */
    public function getEQLQuery() : EQLQueryInterface
    {
        return $this->eqlQuery;
    }

    /**
     * Initializes default field mapping
     */
    protected function initDefaultFieldMapping()
    {
        $parentComponentTable = $this->eqlQuery->getParentComponentTable();
        foreach ($parentComponentTable->getColumns() as $column) {
            $fieldName = $column->getEntityFieldName();
            $prefixedFieldName = "{$parentComponentTable->getEntityName()}.$fieldName";
            $this->mapField(
                $fieldName,
                '{' . $prefixedFieldName . '}',
                $this->getParamType($column->getDataType())
            );
        }

        $componentParentTables = $this->eqlQuery->getComponentParentTables();
        foreach ($componentParentTables as $componentParentTable) {
            $entityName = $componentParentTable->getEntityName();
            foreach ($componentParentTable->getColumns() as $column) {
                $fieldName = $column->getEntityFieldName();
                $queryFieldName = lcfirst($entityName) . ".$fieldName";
                $eqlFieldName = "$entityName.$fieldName";
                $this->mapField(
                    $queryFieldName,
                    '{' . $eqlFieldName . '}',
                    $this->getParamType($column->getDataType())
                );
            }
        }
    }
}