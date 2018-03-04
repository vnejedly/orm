<?php
namespace Hooloovoo\ORM\EventDispatcher;

use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\Persistence\EntityManagerInterface;

/**
 * Class NullConnector
 */
class NullConnector implements ConnectorInterface
{
    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     */
    public function beforeCreate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $inputEntity)
    {
        // TODO: Implement beforeCreate() method.
    }

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     * @param int $primaryKey
     */
    public function afterCreate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $inputEntity, int $primaryKey)
    {
        // TODO: Implement afterCreate() method.
    }

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function beforeUpdate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $modifiedEntity)
    {
        // TODO: Implement beforeUpdate() method.
    }

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function afterUpdate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $modifiedEntity)
    {
        // TODO: Implement afterUpdate() method.
    }

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function beforeDelete(string $eventName, EntityManagerInterface $manager, DataObjectInterface $entity)
    {
        // TODO: Implement beforeDelete() method.
    }

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function afterDelete(string $eventName, EntityManagerInterface $manager, DataObjectInterface $entity)
    {
        // TODO: Implement afterDelete() method.
    }
}