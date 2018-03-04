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
$deserializerDir = $projectRoot . '/src/' . $persistence['directory'] . '/Deserializer';
$exportEntityDir = $projectRoot . '/export/src/' . $persistence['directory'] . '/Entity';
$exportDeserializerDir = $projectRoot . '/export/src/' . $persistence['directory'] . '/Deserializer';
$exportConfigDir = $projectRoot . '/export/config';

$entityNamespace = $persistence['namespace'] . '\\Entity';
$descriptorNamespace = $persistence['namespace'] . '\\Descriptor';
$deserializerNamespace = $persistence['namespace'] . '\\Deserializer';
$managerNamespace = $persistence['namespace'] . '\\Manager';

if (!is_dir($entityDir)) mkdir($entityDir);
if (!is_dir($descriptorDir)) mkdir($descriptorDir);
if (!is_dir($managerDir)) mkdir($managerDir);
if (!is_dir($deserializerDir)) mkdir($deserializerDir);
if (!is_dir($exportEntityDir)) mkdir($exportEntityDir);
if (!is_dir($exportDeserializerDir)) mkdir($exportDeserializerDir);

$servicesPattern = new \Hooloovoo\Generator\Pattern\SingleFile(
    "$exportConfigDir/services-temp.yml",
    __DIR__ . '/../template/services-yml/envelope.yml',
    __DIR__ . '/../template/services-yml/item-persistence.php'
);
$servicesPattern->setContentPlaceholder('{{{_content_persistence_}}}');

/** @var \Hooloovoo\Generator\Generator $entityManagerGenerator */
$generator = $container->get(\Hooloovoo\Generator\Generator::class);
$generator->setExternalVariable('projectNamespace', $projectNamespace);
$generator->setExternalVariable('entityNamespace', $entityNamespace);
$generator->setExternalVariable('entityManagerNamespace', $managerNamespace);
$generator->setExternalVariable('tableDescriptorNamespace', $descriptorNamespace);
$generator->setExternalVariable('deserializerNamespace', $deserializerNamespace);
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($entityDir, __DIR__ . '/../template/persistence/entity.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportEntityDir, __DIR__ . '/../template/persistence/entity.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($descriptorDir, __DIR__ . '/../template/persistence/table-descriptor.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($managerDir, __DIR__ . '/../template/persistence/entity-manager.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($deserializerDir, __DIR__ . '/../template/persistence/deserializer.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportDeserializerDir, __DIR__ . '/../template/persistence/deserializer-export.php'));
$generator->addPattern($servicesPattern);
$generator->run();