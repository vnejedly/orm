<?php
namespace Hooloovoo\ORM\Cache;

/**
 * Interface RegisterInterface
 */
interface RegisterInterface
{
    /**
     * @param string $name
     * @return CacheInterface
     */
    public function getCache(string $name) : CacheInterface ;
}