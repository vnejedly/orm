<?php
namespace Hooloovoo\ORM;

use Hooloovoo\DataObjects\DataObjectInterface;

/**
 * Interface DeserializerInterface
 */
interface DeserializerInterface
{
    /**
     * @param array $data
     * @return DataObjectInterface
     */
    public function deserialize(array $data);
}