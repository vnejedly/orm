<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $projectNamespace
 * @var string $className
 * @var string $relationEntityNamespace
 * @var string $persistenceEntityNamespace
 * @var string $persistenceDeserializerNamespace
 * @var string $relationDeserializerNamespace
 * @var string[] $importEntities
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\FieldInfo[] $fields
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable $parentComponent
 * @var bool $hasCollections
 */ ?>
namespace <?= $relationDeserializerNamespace ?>;

use <?= $projectNamespace ?>\Relation\AbstractDeserializer;
use <?= $relationEntityNamespace ?>\<?= $className ?> as Entity;
use <?= $persistenceDeserializerNamespace ?>\<?= $parentComponent->getComponentEntityName() ?> as ParentDeserializer;
<?php foreach ($importEntities as $entity): ?>
use <?= $persistenceDeserializerNamespace ?>\<?= $entity ?>;
<?php endforeach; ?>

/**
 * Class <?= $className ?>
 *
 * +-------------------------------------------------------+
 * ! this class is auto-generated, please do not change it !
 * +-------------------------------------------------------+
 *
 * <?= $generatedDateTime->format('c') ?>
 */
class <?= $className ?> extends AbstractDeserializer
{
    /**
     * @param ParentDeserializer $parentDeserializer
    <?php foreach ($fields as $field): ?>
     * @param <?= $field->getFieldEntityName() ?> $<?= $field->getFieldName() ?>Deserializer
    <?php endforeach; ?>
     */
    public function __construct(
        ParentDeserializer $parentDeserializer,
        <?php foreach ($fields as $field): ?>
        <?= $field->getFieldEntityName() ?> $<?= $field->getFieldName() ?>Deserializer<?= $this->_delimit($fields, ',') ?>
        <?php endforeach; ?>
    ) {
        $this->setParentDeserializer($parentDeserializer);
        <?php foreach ($fields as $field): ?>
        $this->addDeserializer('<?= $field->getFieldName() ?>', $<?= $field->getFieldName() ?>Deserializer);
        <?php endforeach; ?>
    }

    /**
     * @param array $data
     * @return Entity
     */
    public function deserialize(array $data)
    {
        if (is_null($data)) {
            return null;
        }

        return new Entity(
            $this->deserializeParent($data),
            <?php foreach ($fields as $field): ?>
            <?php if ($field->isCollection()): ?>
            $this->deserializeCollection('<?= $field->getFieldName() ?>', $data['<?= $field->getFieldName() ?>'])<?= $this->_delimit($fields, ',') ?>
            <?php else: ?>
            $this->deserializeField('<?= $field->getFieldName() ?>', $data['<?= $field->getFieldName() ?>'])<?= $this->_delimit($fields, ',') ?>
            <?php endif; ?>
            <?php endforeach; ?>
        );
    }
}
