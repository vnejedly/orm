<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Joint;

use Hooloovoo\DatabaseMapping\ColumnFK;

/**
 * Class InParent
 */
class InParent extends AbstractJoint
{
    /**
     * @return int
     */
    public function getCardinality() : int
    {
        return self::CARDINALITY_ONE;
    }

    /**
     * @return bool
     */
    public function isNullAble() : bool
    {
        return $this->getReferencingColumn()->getIsNullAble();
    }

    /**
     * @return string[]
     */
    public function getJoinClauses(): array
    {
        $parentEntityName = $this->_parent->getComponentEntityName();
        $parentFieldName = $this->getReferencingColumn()->getEntityFieldName();

        $childEntityName = $this->getChild()->getComponentTableMapping()->getEntityName();
        $childFieldName = $this->_child->getComponentTableMapping()->getSimplePrimaryKey()->getEntityFieldName();

        $childEntityStr = '{' . $childEntityName . '}';

        $parentFieldStr = '{' . $parentEntityName . '.' . $parentFieldName . '}';
        $childFieldStr = '{' . $childEntityName . '.' . $childFieldName .'}';

        return [
            "LEFT JOIN $childEntityStr ON $parentFieldStr = $childFieldStr"
        ];
    }

    /**
     * @return ColumnFK
     */
    protected function getReferencingColumn() : ColumnFK
    {
        return $this->_parent->getComponentTableMapping()->getReferencingColumn(
            $this->_child->getComponentTableMapping()->getName()
        );
    }
}