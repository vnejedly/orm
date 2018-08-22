<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $genericManagerNamespace
 * @var string $managerNamespace
 * @var string $tableDescriptorNamespace
 * @var string $entityNamespace
 * @var string $managerName
 * @var string $tableDescriptorName
 * @var string $entityName
 * @var \Hooloovoo\ORM\Generator\Persistence\FieldInfo[] $fields
 * @var \Hooloovoo\ORM\Generator\Persistence\FieldInfo[] $nonPKFields
 * @var string[] $imports
 * @var string[] $valueClassImports
 */ ?>
namespace <?= $managerNamespace ?>;

use <?= $genericManagerNamespace ?>\<?= $managerName ?> as GenericManager;

/**
* Class <?= $managerName ?>
*
* +-----------------------------------------------------------+
* ! this class is an auto-generated stub - write anything you !
* ! want into it, it will be preserved by next generator run  !
* +-----------------------------------------------------------+
*/
class <?= $managerName ?> extends GenericManager
{
    // TODO: implement custom methods here
}