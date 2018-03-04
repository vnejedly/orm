<?php
namespace Hooloovoo\ORM\Generator\Relation;

use Hooloovoo\Database\Database;
use Hooloovoo\Database\DI\ContainerDefinition as DatabaseDefinition;
use Hooloovoo\DatabaseMapping\Descriptor\Schema\FromDatabase;
use Hooloovoo\DatabaseMapping\Schema;
use Hooloovoo\DI\Container\ContainerInterface;
use Hooloovoo\DI\Definition\AbstractDefinitionClass;
use Hooloovoo\DI\ObjectHolder\Singleton;
use Hooloovoo\Generator\Generator;
use Hooloovoo\ORM\Generator\Relation\Definer\Joint\JointFactory;

/**
 * Class Container
 */
class ContainerDefinition extends AbstractDefinitionClass
{
    /** @var array */
    protected $config;

    /**
     * Container constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setUpContainer(ContainerInterface $container)
    {
        $container->addDefinitionClass(new DatabaseDefinition(
            $this->config['database']['host'],
            $this->config['database']['schema'],
            $this->config['database']['user'],
            $this->config['database']['password']
        ));

        $container->add(FromDatabase::class, new Singleton(function () use ($container) {
            return new FromDatabase($container->get(Database::class), $this->config['database']['schema']);
        }));

        $container->add(Schema::class, new Singleton(function () use ($container) {
            return new Schema($container->get(FromDatabase::class));
        }));

        $container->add(DefinerCollection::class, new Singleton(function () use ($container) {
            return new DefinerCollection(
                $container->get(Schema::class),
                $container->get(JointFactory::class),
                $this->config['relation']
            );
        }));

        $container->add(Generator::class, new Singleton(function () use ($container) {
            return new Generator($container->get(Resolver::class));
        }));
    }
}