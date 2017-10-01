<?php
namespace Hooloovoo\ORM\Cache;

/**
 * Class AbstractRegister
 */
abstract class AbstractRegister implements RegisterInterface
{
    /** @var CacheInterface[] */
    protected $_caches = [];

    /**
     * @param string $name
     * @return CacheInterface
     */
    public function getCache(string $name) : CacheInterface
    {
        if (!array_key_exists($name, $this->_caches)) {
            $this->_caches[$name] = $this->getNew();
        }

        return $this->_caches[$name];
    }

    /**
     * @return CacheInterface
     */
    abstract protected function getNew() : CacheInterface ;
}