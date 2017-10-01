<?php
namespace Hooloovoo\ORM\Generator\Persistence;

use Hooloovoo\DatabaseMapping\Table;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldObjectDef;
use Generator;

/**
 * Class EntityManagersResolver
 */
class EntityManagersResolver extends AbstractResolver
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

            echo "Generating entity managers for table '{$table->getName()}'\n";
            yield $this->resolveVariables($table);
        }
    }

    /**
     * @param Table $table
     * @return array
     */
    protected function resolveVariables(Table $table)
    {
        $entityName = $table->getEntityName();
        $fileName = "{$entityName}.php";

        $fields = [];
        $nonPKFields = [];
        $imports = [];
        foreach ($table->getColumns() as $column) {
            $type = $column->getDataType();
            $fieldDef = $this->_mapping->getFieldDefinition($type);

            if ($fieldDef instanceof FieldObjectDef) {
                $this->_arrayAdd($imports, [$fieldDef->getValueClassImport()]);
            }

            $field = new FieldInfo(
                $fieldDef,
                $column
            );

            $fields[$column->getColumnName()] = $field;

            if (!$column->getIsAutoIncrement()) {
                $nonPKFields[$column->getColumnName()] = $field;
            }
        }

        return [
            'fileName' => $fileName,
            'entityName' => $entityName,
            'managerName' => $entityName,
            'tableDescriptorName' => $entityName,
            'fields' => $fields,
            'nonPKFields' => $nonPKFields,
            'imports' => $imports,
        ];
    }
}