<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $className
 * @var string $relationEntityNamespace
 * @var string $persistenceEntityNamespace
 * @var string[] $importEntities
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\FieldInfo[] $fields
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable $parentComponent
 * @var bool $hasCollections
 */ ?>
namespace <?= $relationEntityNamespace ?>;

use Hooloovoo\DataObjects\Field\FieldDataObject;
<?php if ($hasCollections): ?>
use Hooloovoo\DataObjects\Field\FieldCollection;
<?php endif; ?>
use <?= $persistenceEntityNamespace ?>\<?= $parentComponent->getComponentEntityName() ?> as ParentComponent;
<?php foreach ($importEntities as $entity): ?>
use <?= $persistenceEntityNamespace ?>\<?= $entity ?>;
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
class <?= $className ?> extends ParentComponent
{
    /**
     * Entity constructor.
     *
     * @param ParentComponent $<?= $parentComponent->getComponentFieldName() ?>
     <?php foreach ($fields as $field): ?>
     * @param <?= $field->getAnnotation() ?>
     <?php endforeach; ?>
     */
    public function __construct(
        ParentComponent $<?= $parentComponent->getComponentFieldName() ?>,
        <?php foreach ($fields as $field): ?>
        <?= $field->getDeclaration() ?><?= $this->_delimit($fields, ',') ?>
        <?php endforeach; ?>
    ) {
        $this->fetchSuperclassFields($<?= $parentComponent->getComponentFieldName() ?>);
        <?php foreach ($fields as $field): ?>
        $this->addField('<?= $field->getFieldName() ?>', new <?= $field->getFieldClass() ?>($<?= $field->getFieldName() ?>));
        <?php endforeach; ?>
    }
}
