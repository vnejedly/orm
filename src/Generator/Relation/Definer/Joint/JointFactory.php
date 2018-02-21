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
     * @param string $mapTableName
     * @return JointInterface
     */
    public function getJoint(
        ComponentTable $parent,
        ComponentInterface $child,
        string $mapTableName = null
    ) : JointInterface
    {
        if (!is_null($mapTableName)) {
            return new InTable($parent, $child, $this->_schema->getMapTable(
                $parent->getComponentTableMapping()->getName(),
                $child->getComponentTableMapping()->getName(),
                $mapTableName
            ));
        }

        if ($parent->getComponentTableMapping()->hasReference($child->getComponentTableMapping()->getName())) {
            return new InParent($parent, $child);
        }

        if ($child->getComponentTableMapping()->hasReference($parent->getComponentTableMapping()->getName())) {
            return new InChild($parent, $child);
        }

        return new InTable($parent, $child, $this->_schema->findMapTable(
            $parent->getComponentTableMapping()->getName(),
            $child->getComponentTableMapping()->getName()
        ));
    }
}