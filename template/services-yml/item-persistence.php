<?php /**
 * @var string $deserializerNamespace
 * @var string $entityName
 */ ?>
    persistence.deserializer.<?= lcfirst($entityName) ?>:
        class:  <?= $deserializerNamespace ?>\<?= $entityName ?>
