<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $className
 * @var string $schemaName
 * @var string $tableName
 * @var string $tableDescriptorNamespace
 * @var array[] $columnsInfo
 */ ?>
namespace <?= $tableDescriptorNamespace ?>;

use Hooloovoo\DatabaseMapping\Descriptor\Table\TableInterface;

/**
 * Class <?= $className ?>
 *
 * +-------------------------------------------------------+
 * ! this class is auto-generated, please do not change it !
 * +-------------------------------------------------------+
 */
class <?= $className ?> implements TableInterface
{
    /**
     * @return string
     */
    public function getSchemaName() : string
    {
        return '<?= $schemaName ?>';
    }

    /**
     * @return string
     */
    public function getTableName() : string
    {
        return '<?= $tableName ?>';
    }

    /**
     * @return array
     */
    public function getArray() : array
    {
        return [
            <?php foreach ($columnsInfo as $column): ?>
            [
                <?php foreach ($column as $key => $value): ?>
                '<?= $key ?>' => <?= var_export($value, true) ?>,
                <?php endforeach; ?>
            ],
            <?php endforeach; ?>
        ];
    }
}