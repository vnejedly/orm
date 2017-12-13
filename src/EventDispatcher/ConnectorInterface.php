<?php
namespace Hooloovoo\ORM\EventDispatcher;

use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\EntityManager\EntityManagerInterface;

/**
 * Interface ConnectorInterface
 */
interface ConnectorInterface
{
    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     */
    public function beforeCreate(EntityManagerInterface $manager, DataObjectInterface $inputEntity);

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $inputEntity
     * @param int $primaryKey
     */
    public function afterCreate(EntityManagerInterface $manager, DataObjectInterface $inputEntity, int $primaryKey);

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function beforeUpdate(EntityManagerInterface $manager, DataObjectInterface $modifiedEntity);

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $modifiedEntity
     */
    public function afterUpdate(EntityManagerInterface $manager, DataObjectInterface $modifiedEntity);

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function beforeDelete(EntityManagerInterface $manager, DataObjectInterface $entity);

    /**
     * @param EntityManagerInterface $manager
     * @param DataObjectInterface $entity
     */
    public function afterDelete(EntityManagerInterface $manager, DataObjectInterface $entity);
}