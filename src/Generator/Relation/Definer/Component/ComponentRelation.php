<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Component;

use Hooloovoo\DatabaseMapping\Table;
use Hooloovoo\ORM\Generator\Relation\Definer\Definer;

/**
 * Class ChildRelation
 */
class ComponentRelation extends AbstractComponent
{
    /** @var Definer */
    protected $_definer;

    /**
     * ComponentRelation constructor.
     *
     * @param Definer $definer
     */
    public function __construct(Definer $definer)
    {
        $this->_definer = $definer;
    }

    /**
     * @return string
     */
    public function getComponentEntityName() : string
    {
        return $this->_definer->getName();
    }

    /**
     * @return Table
     */
    public function getComponentTableMapping() : Table
    {
        return $this->_definer->getParent()->getComponentTableMapping();
    }
}