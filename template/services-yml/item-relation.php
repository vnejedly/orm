<?php /**
 * @var string $className
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\FieldInfo[] $fields
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable $parentComponent
 */ ?>
    relation.deserializer.<?= lcfirst($className) ?>:
        class:  <?= $relationDeserializerNamespace ?>\<?= $className ?>
        arguments:
            $parentDeserializer: '@persistence.deserializer.<?= lcfirst($parentComponent->getComponentEntityName()) ?>'
            <?php foreach ($fields as $field): ?>
            $<?= $field->getFieldName() ?>Deserializer: '<?=
                ($field->isPersistence())
                ? '@persistence.deserializer.' . lcfirst($field->getFieldEntityName())
                : '@relation.deserializer.' . lcfirst($field->getFieldEntityName())
            ?>'
            <?php endforeach; ?>
