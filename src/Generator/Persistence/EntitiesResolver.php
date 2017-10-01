<?php
namespace Hooloovoo\ORM\Generator\Persistence;

use Hooloovoo\DatabaseMapping\Table;
use Generator;

/**
 * Class EntitiesResolver
 */
class EntitiesResolver extends AbstractResolver
{
    /**
     * @return Generator
     */
    public function yieldVariables() : Generator
    {
        foreach ($this->_schema->getTables() as $table) {
            if (!$table->hasPrimaryKey() || $table->hasMultiplePrimaryKey()) {
                continue;
            }

            echo "Generating entities for table '{$table->getName()}'\n";
            yield $this->resolveVariables($table);
        }
    }

    /**
     * @param Table $table
     * @return array
     */
    protected function resolveVariables(Table $table)
    {
        $className = $table->getEntityName();
        $fileName = "{$className}.php";

        $imports = [];
        $fields = [];
        foreach ($table->getColumns() as $column) {
            $type = $column->getDataType();
            $fieldDef = $this->_mapping->getFieldDefinition($type);

            $this->_arrayAdd($imports, $fieldDef->getImports());
            $fields[$column->getColumnName()] = new FieldInfo(
                $fieldDef,
                $column
            );
        }

        return [
            'fileName' => $fileName,
            'className' => $className,
            'fields' => $fields,
            'imports' => $imports,
        ];
    }
}