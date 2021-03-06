<?php
namespace Hooloovoo\ORM\DI;

use Hooloovoo\DI\ObjectHolder\Singleton;
use Hooloovoo\ORM\EventDispatcher\ConnectorInterface as DispatcherConnectorInterface;
use Hooloovoo\ORM\EventDispatcher\NullConnector as NullDispatcherConnector;
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

    /** @var bool */
    protected $emulatePrepares;

    /**
     * Container constructor
     *
     * @param string $host
     * @param string $database
     * @param string $user
     * @param string $password
     * @param bool $emulatePrepares
     */
    public function __construct(
        string $host,
        string $database,
        string $user,
        string $password,
        bool $emulatePrepares = true
    ) {
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
        $this->emulatePrepares = $emulatePrepares;
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
            $this->password,
            $this->emulatePrepares
        ));

        $container->add(DispatcherConnectorInterface::class, new Singleton(function () use ($container) {
            return $container->get(NullDispatcherConnector::class);
        }));
    }
}