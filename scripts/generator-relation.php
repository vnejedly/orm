<?php

$projectRoot = __DIR__ . '/../../../..';

require_once $projectRoot . "/vendor/autoload.php";

$config = json_decode(file_get_contents("$projectRoot/config/orm.json"), true);

$projectNamespace = $config['projectNamespace'];
$database = $config['database'];
$relation = $config['relation'];
$persistence = $config['persistence'];

$container = new \Hooloovoo\DI\Container\Container();
$container->addDefinitionClass(new \Hooloovoo\ORM\Generator\Relation\ContainerDefinition($config));

$entityDir = $projectRoot . '/src/' . $relation['directory'] . '/Entity';
$genericManagerDir = $projectRoot . '/src/' . $relation['directory'] . '/GenericManager';
$managerDir = $projectRoot . '/src/' . $relation['directory'] . '/Manager';
$exportEntityDir = $projectRoot . '/export/src/' . $relation['directory'] . '/Entity';
$exportDeserializerDir = $projectRoot . '/export/src/' . $relation['directory'] . '/Deserializer';
$exportConfigDir = $projectRoot . '/export/config';

$relationEntityNamespace = $relation['namespace'] . '\\Entity';
$persistenceEntityNamespace = $persistence['namespace'] . '\\Entity';
$relationManagerGenericNamespace = $relation['namespace'] . '\\GenericManager';
$relationManagerNamespace = $relation['namespace'] . '\\Manager';
$relationDeserializerNamespace = $relation['namespace'] . '\\Deserializer';
$persistenceManagerNamespace = $persistence['namespace'] . '\\Manager';
$persistenceDeserializerNamespace = $persistence['namespace'] . '\\Deserializer';

mkdir($entityDir);
mkdir($genericManagerDir);
mkdir($managerDir);
mkdir($exportEntityDir);
mkdir($exportDeserializerDir);

$servicesPattern = new \Hooloovoo\Generator\Pattern\SingleFile(
    "$exportConfigDir/services.yml",
    "$exportConfigDir/services-temp.yml",
    __DIR__ . '/../template/services-yml/item-relation.php'
);
$servicesPattern->setContentPlaceholder('{{{_content_relation_}}}');

/** @var \Hooloovoo\Generator\Generator $entitiesGenerator */
$entitiesGenerator = $container->get(\Hooloovoo\ORM\Generator\Relation\ContainerDefinition::ENTITIES_GENERATOR);
$entitiesGenerator->setExternalVariable('projectNamespace', $projectNamespace);
$entitiesGenerator->setExternalVariable('persistenceEntityNamespace', $persistenceEntityNamespace);
$entitiesGenerator->setExternalVariable('relationEntityNamespace', $relationEntityNamespace);
$entitiesGenerator->setExternalVariable('persistenceDeserializerNamespace', $persistenceDeserializerNamespace);
$entitiesGenerator->setExternalVariable('relationDeserializerNamespace', $relationDeserializerNamespace);
$entitiesGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($entityDir, __DIR__ . '/../template/relation/entity.php'));
$entitiesGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportEntityDir, __DIR__ . '/../template/relation/entity.php'));
$entitiesGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportDeserializerDir, __DIR__ . '/../template/relation/deserializer.php'));
$entitiesGenerator->addPattern($servicesPattern);
$entitiesGenerator->run();

/** @var \Hooloovoo\Generator\Generator $genericManagersGenerator */
$genericManagersGenerator = $container->get(\Hooloovoo\ORM\Generator\Relation\ContainerDefinition::MANAGERS_GENERATOR);
$genericManagersGenerator->setExternalVariable('persistenceManagerNamespace', $persistenceManagerNamespace);
$genericManagersGenerator->setExternalVariable('relationManagerGenericNamespace', $relationManagerGenericNamespace);
$genericManagersGenerator->setExternalVariable('relationManagerNamespace', $relationManagerNamespace);
$genericManagersGenerator->setExternalVariable('relationEntityNamespace', $relationEntityNamespace);
$genericManagersGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($genericManagerDir, __DIR__ . '/../template/relation/generic-manager.php'));
$genericManagersGenerator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($managerDir, __DIR__ . '/../template/relation/manager.php', false));
$genericManagersGenerator->run();

unlink("$exportConfigDir/services-temp.yml");