<?php
namespace Hooloovoo\ORM\Generator\Persistence;

use Hooloovoo\Database\Database;
use Hooloovoo\Database\DI\ContainerDefinition as DatabaseDefinition;
use Hooloovoo\DatabaseMapping\Descriptor\Schema\FromDatabase;
use Hooloovoo\DatabaseMapping\Schema;
use Hooloovoo\DI\Container\ContainerInterface;
use Hooloovoo\DI\Definition\AbstractDefinitionClass;
use Hooloovoo\DI\ObjectHolder\Singleton;
use Hooloovoo\Generator\Generator;

/**
 * Class ContainerDefinition
 */
class ContainerDefinition extends AbstractDefinitionClass
{
    /** @var string */
    protected $host;

    /** @var string */
    protected $database;

    /** @var string */
    protected $user;

    /** @var string */
    protected $password;

    /**
     * Container constructor
     *
     * @param string $host
     * @param string $database
     * @param string $user
     * @param string $password
     */
    public function __construct(
        string $host,
        string $database,
        string $user,
        string $password
    ) {
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setUpContainer(ContainerInterface $container)
    {
        $container->addDefinitionClass(new DatabaseDefinition(
            $this->host,
            $this->database,
            $this->user,
            $this->password
        ));

        $container->add(FromDatabase::class, new Singleton(function () use ($container) {
            return new FromDatabase($container->get(Database::class), $this->database);
        }));

        $container->add(Schema::class, new Singleton(function () use ($container) {
            return new Schema($container->get(FromDatabase::class));
        }));

        $container->add(Generator::class, new Singleton(function () use ($container) {
            return new Generator($container->get(Resolver::class));
        }));
    }
}