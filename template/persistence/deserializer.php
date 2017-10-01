<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $projectNamespace
 * @var string $deserializerNamespace
 * @var string $entityNamespace
 * @var string $entityName
 * @var \Hooloovoo\ORM\Generator\Persistence\FieldInfo[] $fields
 * @var string[] $imports
 */ ?>
namespace <?= $deserializerNamespace ?>;

use <?= $projectNamespace ?>\DeserializerInterface;
use <?= $entityNamespace ?>\<?= $entityName ?> as Entity;
<?php foreach ($imports as $import): ?>
use <?= $import ?>;
<?php endforeach; ?>

/**
 * Class <?= $entityName ?>
 *
 * +-------------------------------------------------------+
 * ! this class is auto-generated, please do not change it !
 * +-------------------------------------------------------+
 *
 * <?= $generatedDateTime->format('c') ?>
 */
class <?= $entityName ?> implements DeserializerInterface
{
    /**
     * @param array $data
     * @return Entity
     */
    public function deserialize(array $data)
    {
        return new Entity(
        <?php foreach ($fields as $field): ?>
            <?php if ($field->isValueObject()): ?>
            new <?= $field->getValueClassAlias() ?>($data['<?= $field->getName() ?>'])<?= $this->_delimit($fields, ',') ?>
            <?php else: ?>
            $data['<?= $field->getName() ?>']<?= $this->_delimit($fields, ',') ?>
            <?php endif; ?>
        <?php endforeach; ?>
        );
    }
}
