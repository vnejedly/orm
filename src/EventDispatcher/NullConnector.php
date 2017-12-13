<?php
namespace Hooloovoo\ORM\EventDispatcher;

use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\EntityManager\EntityManagerInterface;

/**
 * Class NullConnector
 */
class NullConnector implements ConnectorInterface
{
    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     */
    public function beforeCreate(EntityManagerInterface $manager, DataObjectInterface $inputEntity)
    {
        // TODO: Implement beforeCreate() method.
    }

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     * @param int $primaryKey
     */
    public function afterCreate(EntityManagerInterface $manager, DataObjectInterface $inputEntity, int $primaryKey)
    {
        // TODO: Implement afterCreate() method.
    }

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function beforeUpdate(EntityManagerInterface $manager, DataObjectInterface $modifiedEntity)
    {
        // TODO: Implement beforeUpdate() method.
    }

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function afterUpdate(EntityManagerInterface $manager, DataObjectInterface $modifiedEntity)
    {
        // TODO: Implement afterUpdate() method.
    }

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function beforeDelete(EntityManagerInterface $manager, DataObjectInterface $entity)
    {
        // TODO: Implement beforeDelete() method.
    }

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function afterDelete(EntityManagerInterface $manager, DataObjectInterface $entity)
    {
        // TODO: Implement afterDelete() method.
    }
}