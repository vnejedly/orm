<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Component;

/**
 * Class AbstractComponent
 */
abstract class AbstractComponent implements ComponentInterface
{
    /**
     * @return string
     */
    public function getComponentFieldName() : string
    {
        return lcfirst($this->getComponentTableMapping()->getEntityName());
    }
}