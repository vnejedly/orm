<?php
namespace Hooloovoo\ORM\Cache\InArray;

use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\Cache\CacheInterface;

/**
 * Class Cache
 */
class Cache implements CacheInterface
{
    /** @var DataObjectInterface[] */
    protected $_storage = [];

    /**
     * @param int $key
     * @return bool
     */
    public function exists(int $key) : bool
    {
        return array_key_exists($key, $this->_storage);
    }

    /**
     * @param int $key
     * @return DataObjectInterface
     */
    public function get(int $key) : DataObjectInterface
    {
        return $this->_storage[$key];
    }

    /**
     * @param int[] $keys
     * @return DataObjectInterface[]
     */
    public function getMultiple(array $keys) : array
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->exists($key)) {
                $result[$key] = $this->get($key);
            } else {
                $result[$key] = null;
            }
        }

        return $result;
    }

    /**
     * @param int $key
     * @param DataObjectInterface $dataObject
     */
    public function set(int $key, DataObjectInterface $dataObject)
    {
        $this->_storage[$key] = $dataObject;
    }

    /**
     * @param DataObjectInterface[] $dataObjects
     */
    public function setMultiple(array $dataObjects)
    {
        foreach ($dataObjects as $key => $dataObject) {
            $this->set($key, $dataObject);
        }
    }

    /**
     * @param int $key
     */
    public function delete(int $key)
    {
        unset($this->_storage[$key]);
    }

    /**
     */
    public function clear()
    {
        $this->_storage = [];
    }
}