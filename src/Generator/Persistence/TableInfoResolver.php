<?php
namespace Hooloovoo\ORM\Generator\Persistence;

use Hooloovoo\DatabaseMapping\Table;
use Generator;

/**
 * Class TableInfoResolver
 */
class TableInfoResolver extends AbstractResolver
{
    /**
     * @return Generator
     */
    public function yieldVariables() : Generator
    {
        foreach ($this->_schema->getTables() as $table) {
            echo "Generating table info classes for table '{$table->getName()}'\n";
            yield $this->resolveVariables($table);
        }
    }

    /**
     * @param Table $table
     * @return array
     */
    protected function resolveVariables(Table $table)
    {
        $tableName = $table->getName();
        $className = $table->getEntityName();
        $fileName = "{$className}.php";

        return [
            'fileName' => $fileName,
            'className' => $className,
            'tableName' => $tableName,
            'schemaName' => $table->getDescriptor()->getSchemaName(),
            'columnsInfo' => $table->getDescriptor()->getArray(),
        ];
    }
}