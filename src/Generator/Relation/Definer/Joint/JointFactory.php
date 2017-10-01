<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Joint;

use Hooloovoo\DatabaseMapping\Schema;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentInterface;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable;

/**
 * Class JointFactory
 */
class JointFactory
{
    /** @var Schema */
    protected $_schema;

    /**
     * JointFactory constructor.
     *
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->_schema = $schema;
    }

    /**
     * @param ComponentTable $parent
     * @param ComponentInterface $child
     * @return JointInterface
     */
    public function getJoint(ComponentTable $parent, ComponentInterface $child) : JointInterface
    {
        if ($parent->getComponentTableMapping()->hasReference($child->getComponentTableMapping()->getName())) {
            return new InParent($parent, $child);
        } elseif ($child->getComponentTableMapping()->hasReference($parent->getComponentTableMapping()->getName())) {
            return new InChild($parent, $child);
        } else {
            return new InTable($parent, $child, $this->_schema->findMapTable(
                $parent->getComponentTableMapping()->getName(),
                $child->getComponentTableMapping()->getName()
            ));
        }
    }
}