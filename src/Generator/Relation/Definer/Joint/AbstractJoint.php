<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Joint;

use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentInterface;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable;

/**
 * Class AbstractJoint
 */
abstract class AbstractJoint implements JointInterface
{
    /** @var ComponentTable */
    protected $_parent;

    /** @var ComponentInterface */
    protected $_child;

    /**
     * AbstractJoint constructor.
     *
     * @param ComponentTable $parent
     * @param ComponentInterface $child
     */
    public function __construct(ComponentTable $parent, ComponentInterface $child)
    {
        $this->_parent = $parent;
        $this->_child = $child;
    }

    /**
     * @return ComponentTable
     */
    public function getParent() : ComponentTable
    {
        return $this->_parent;
    }

    /**
     * @return ComponentInterface
     */
    public function getChild() : ComponentInterface
    {
        return $this->_child;
    }
}