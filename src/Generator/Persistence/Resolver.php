<?php
namespace Hooloovoo\ORM\Generator\Persistence;

use Generator;
use Hooloovoo\DatabaseMapping\Table;
use Hooloovoo\DatabaseMapping\Schema;
use Hooloovoo\Generator\ResolverInterface;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\Mapping;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldObjectDef;

/**
 * Class Resolver
 */
class Resolver implements ResolverInterface
{
    /** @var Schema */
    protected $_schema;

    /** @var Mapping */
    protected $_mapping;

    /**
     * EntityManagersResolver constructor.
     * @param Schema $schema
     * @param Mapping $mapping
     */
    public function __construct(Schema $schema, Mapping $mapping)
    {
        $this->_schema = $schema;
        $this->_mapping = $mapping;
    }

    /**
     * @return Generator
     */
    public function yieldVariables() : Generator
    {
        foreach ($this->_schema->getTables() as $table) {
            if (!$table->hasPrimaryKey() || $table->hasMultiplePrimaryKey()) {
                continue;
            }

            echo "Generating persitence layer for table '{$table->getName()}'\n";
            yield $this->resolveVariables($table);
        }
    }

    /**
     * @param Table $table
     * @return array
     */
    protected function resolveVariables(Table $table)
    {
        $schemaName = $table->getDescriptor()->getSchemaName();
        $tableName = $table->getName();
        $className = $entityName = $table->getEntityName();
        $fileName = "{$entityName}.php";
        $columnsInfo = $table->getDescriptor()->getArray();

        $fields = [];
        $nonPKFields = [];
        $imports = [];
        $valueClassImports = [];
        foreach ($table->getColumns() as $column) {
            $type = $column->getDataType();
            $fieldDef = $this->_mapping->getFieldDefinition($type);

            $this->_arrayAdd($imports, $fieldDef->getImports());

            if ($fieldDef instanceof FieldObjectDef) {
                $import = $fieldDef->getValueClassImport();
                $this->_arrayAdd($imports, [$import]);
                $this->_arrayAdd($valueClassImports, [$import]);
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
            'schemaName' => $schemaName,
            'tableName' => $tableName,
            'fileName' => $fileName,
            'className' => $className,
            'entityName' => $entityName,
            'managerName' => $entityName,
            'columnsInfo' => $columnsInfo,
            'tableDescriptorName' => $entityName,
            'fields' => $fields,
            'nonPKFields' => $nonPKFields,
            'imports' => $imports,
            'valueClassImports' => $valueClassImports,
        ];
    }

    /**
     * @param array $main
     * @param array $additional
     */
    protected function _arrayAdd(array &$main, array $additional)
    {
        foreach ($additional as $item) {
            if (!in_array($item, $main)) {
                $main[] = $item;
            }
        }
    }
}