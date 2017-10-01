<?php

$projectRoot = __DIR__ . '/../../../..';

require_once $projectRoot . "/vendor/autoload.php";

$config = json_decode(file_get_contents("$projectRoot/config/orm.json"), true);

$database = $config['database'];
$projectNamespace = $config['projectNamespace'];
$persistence = $config['persistence'];

$container = new \Hooloovoo\DI\Container\Container();
$container->addDefinitionClass(new \Hooloovoo\ORM\Generator\Persistence\ContainerDefinition(
    $database['host'], $database['schema'], $database['user'],  $database['password']
));

$entityDir = $projectRoot . '/src/' . $persistence['directory'] . '/Entity';
$descriptorDir = $projectRoot . '/src/' . $persistence['directory'] . '/Descriptor';
$managerDir = $projectRoot . '/src/' . $persistence['directory'] . '/Manager';
$exportEntityDir = $projectRoot . '/export/src/' . $persistence['directory'] . '/Entity';
$exportDeserializerDir = $projectRoot . '/export/src/' . $persistence['directory'] . '/Deserializer';
$exportConfigDir = $projectRoot . '/export/config';

$entityNamespace = $persistence['namespace'] . '\\Entity';
$descriptorNamespace = $persistence['namespace'] . '\\Descriptor';
$deserializerNamespace = $persistence['namespace'] . '\\Deserializer';
$managerNamespace = $persistence['namespace'] . '\\Manager';

mkdir($entityDir);
mkdir($descriptorDir);
mkdir($managerDir);
mkdir($exportEntityDir);
mkdir($exportDeserializerDir);

$servicesPattern = new \Hooloovoo\Generator\Pattern\SingleFile(
    "$exportConfigDir/services-temp.yml",
    __DIR__ . '/../template/services-yml/envelope.yml',
    __DIR__ . '/../template/services-yml/item-persistence.php'
);
$servicesPattern->setContentPlaceholder('{{{_content_persistence_}}}');

/** @var \Hooloovoo\Generator\Generator $entitiesGenerator */
$entitiesGenerator = $container->get(\Hooloovoo\ORM\Generator\Persistence\ContainerDefinition::ENTITIES_GENERATOR);
$entitiesGenerator->setExternalVariable('entityNamespace', $entityNamespace);
$entitiesGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($entityDir, __DIR__ . '/../template/persistence/entity.php'));
$entitiesGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportEntityDir, __DIR__ . '/../template/persistence/entity.php'));
$entitiesGenerator->run();

/** @var \Hooloovoo\Generator\Generator $tableInfoGenerator */
$tableInfoGenerator = $container->get(\Hooloovoo\ORM\Generator\Persistence\ContainerDefinition::TABLE_INFO_GENERATOR);
$tableInfoGenerator->setExternalVariable('tableDescriptorNamespace', $descriptorNamespace);
$tableInfoGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($descriptorDir, __DIR__ . '/../template/persistence/table-descriptor.php'));
$tableInfoGenerator->run();

/** @var \Hooloovoo\Generator\Generator $entityManagerGenerator */
$entityManagerGenerator = $container->get(\Hooloovoo\ORM\Generator\Persistence\ContainerDefinition::ENTITY_MANAGERS_GENERATOR);
$entityManagerGenerator->setExternalVariable('projectNamespace', $projectNamespace);
$entityManagerGenerator->setExternalVariable('entityNamespace', $entityNamespace);
$entityManagerGenerator->setExternalVariable('entityManagerNamespace', $managerNamespace);
$entityManagerGenerator->setExternalVariable('tableDescriptorNamespace', $descriptorNamespace);
$entityManagerGenerator->setExternalVariable('deserializerNamespace', $deserializerNamespace);
$entityManagerGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($managerDir, __DIR__ . '/../template/persistence/entity-manager.php'));
$entityManagerGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportDeserializerDir, __DIR__ . '/../template/persistence/deserializer.php'));
$entityManagerGenerator->addPattern($servicesPattern);

$entityManagerGenerator->run();