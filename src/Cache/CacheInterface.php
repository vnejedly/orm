<?php
namespace Hooloovoo\ORM\Cache;

use Hooloovoo\DataObjects\DataObjectInterface;

/**
 * Interface CacheInterface
 */
interface CacheInterface
{
    /**
     * @param int $key
     * @return bool
     */
    public function exists(int $key) : bool ;

    /**
     * @param int $key
     * @return DataObjectInterface
     */
    public function get(int $key) : DataObjectInterface ;

    /**
     * @param int[] $keys
     * @return DataObjectInterface[]
     */
    public function getMultiple(array $keys) : array ;

    /**
     * @param int $key
     * @param DataObjectInterface $dataObject
     */
    public function set(int $key, DataObjectInterface $dataObject);

    /**
     * @param DataObjectInterface[] $dataObjects
     */
    public function setMultiple(array $dataObjects);

    /**
     * @param int $key
     */
    public function delete(int $key);

    /**
     */
    public function clear();
}