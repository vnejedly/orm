<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $className
 * @var string[] $imports
 * @var string $entityNamespace
 * @var \Hooloovoo\ORM\Generator\Persistence\FieldInfo[] $fields
 */ ?>
namespace <?= $entityNamespace ?>;

use Hooloovoo\DataObjects\DataObject;
<?php foreach ($imports as $import): ?>
use <?= $import ?>;
<?php endforeach; ?>

/**
 * Class <?= $className ?>
 *
 * +-------------------------------------------------------+
 * ! this class is auto-generated, please do not change it !
 * +-------------------------------------------------------+
 *
 * <?= $generatedDateTime->format('c') ?>
 *
 <?php foreach ($fields as $field): ?>
 * @property <?= $field->getAnnotation() ?>
 <?php endforeach; ?>
 */
class <?= $className ?> extends DataObject
{
    /**
     * Entity constructor.
     *
     <?php foreach ($fields as $field): ?>
     * @param <?= $field->getAnnotation() ?>
     <?php endforeach; ?>
     */
    public function __construct(
        <?php foreach ($fields as $field): ?>
        <?= $field->getDeclaration() ?><?= $this->_delimit($fields, ',') ?>
        <?php endforeach; ?>
    ) {
        <?php foreach ($fields as $field): ?>
        $this->addField('<?= $field->getName() ?>', new <?= $field->getInstanceCreation() ?>);
        <?php endforeach; ?>
    }
}
