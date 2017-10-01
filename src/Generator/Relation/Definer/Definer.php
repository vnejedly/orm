<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer;

use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentInterface;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable;
use Hooloovoo\ORM\Generator\Relation\Definer\Joint\JointFactory;
use Hooloovoo\ORM\Generator\Relation\Definer\Joint\JointInterface;

/**
 * Class Definer
 */
class Definer
{
    /** @var string */
    protected $_name;

    /** @var ComponentTable */
    protected $_parent;

    /** @var JointFactory */
    protected $_jointFactory;

    /** @var JointInterface[] */
    protected $_children = [];

    /**
     * Definer constructor.
     *
     * @param string $name
     * @param ComponentTable $parent
     * @param JointFactory $jointFactory
     */
    public function __construct(string $name, ComponentTable $parent, JointFactory $jointFactory)
    {
        $this->_name = $name;
        $this->_parent = $parent;
        $this->_jointFactory = $jointFactory;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param ComponentInterface $child
     */
    public function addChild(ComponentInterface $child)
    {
        $this->_children[] = $this->_jointFactory->getJoint($this->_parent, $child);
    }

    /**
     * @return ComponentTable
     */
    public function getParent() : ComponentTable
    {
        return $this->_parent;
    }

    /**
     * @return JointInterface[]
     */
    public function getChildren() : array
    {
        return $this->_children;
    }
}