<?php
namespace Hooloovoo\ORM\EventDispatcher;

use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\Persistence\EntityManagerInterface;

/**
 * Interface ConnectorInterface
 */
interface ConnectorInterface
{
    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     */
    public function beforeCreate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $inputEntity);

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     * @param int $primaryKey
     */
    public function afterCreate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $inputEntity, int $primaryKey);

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function beforeUpdate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $modifiedEntity);

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function afterUpdate(string $eventName, EntityManagerInterface $manager, DataObjectInterface $modifiedEntity);

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function beforeDelete(string $eventName, EntityManagerInterface $manager, DataObjectInterface $entity);

    /**
     * @param string $eventName
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function afterDelete(string $eventName, EntityManagerInterface $manager, DataObjectInterface $entity);
}