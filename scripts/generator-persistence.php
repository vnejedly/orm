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

$entityNamespace = $persistence['namespace'] . '\\Entity';
$descriptorNamespace = $persistence['namespace'] . '\\Descriptor';
$deserializerNamespace = $persistence['namespace'] . '\\Deserializer';
$managerNamespace = $persistence['namespace'] . '\\Manager';

if (!is_dir($entityDir)) mkdir($entityDir);
if (!is_dir($descriptorDir)) mkdir($descriptorDir);
if (!is_dir($managerDir)) mkdir($managerDir);
if (!is_dir($deserializerDir)) mkdir($deserializerDir);

/** @var \Hooloovoo\Generator\Generator $entityManagerGenerator */
$generator = $container->get(\Hooloovoo\Generator\Generator::class);
$generator->setExternalVariable('projectNamespace', $projectNamespace);
$generator->setExternalVariable('entityNamespace', $entityNamespace);
$generator->setExternalVariable('entityManagerNamespace', $managerNamespace);
$generator->setExternalVariable('tableDescriptorNamespace', $descriptorNamespace);
$generator->setExternalVariable('deserializerNamespace', $deserializerNamespace);
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($entityDir, __DIR__ . '/../template/persistence/entity.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($descriptorDir, __DIR__ . '/../template/persistence/table-descriptor.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($managerDir, __DIR__ . '/../template/persistence/entity-manager.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($deserializerDir, __DIR__ . '/../template/persistence/deserializer.php'));
$generator->run();