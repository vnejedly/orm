<?php
namespace Hooloovoo\ORM\Cache\InArray;

use Hooloovoo\ORM\Cache\AbstractRegister;
use Hooloovoo\ORM\Cache\CacheInterface;

/**
 * Class Register
 */
class Register extends AbstractRegister
{
    /**
     * @return CacheInterface
     */
    protected function getNew() : CacheInterface
    {
        return new Cache();
    }
}