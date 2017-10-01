<?php
namespace Hooloovoo\ORM\DI;

use Hooloovoo\Database\DI\ContainerDefinition as DatabaseDefinition;
use Hooloovoo\DI\Container\ContainerInterface;
use Hooloovoo\DI\Definition\AbstractDefinitionClass;

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
    }
}