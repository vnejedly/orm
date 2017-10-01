<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Component;

use Hooloovoo\DatabaseMapping\Table;

/**
 * Interface ComponentInterface
 */
interface ComponentInterface
{
    /**
     * @return string
     */
    public function getComponentEntityName() : string ;

    /**
     * @return string
     */
    public function getComponentFieldName() : string ;

    /**
     * @return Table
     */
    public function getComponentTableMapping() : Table ;
}