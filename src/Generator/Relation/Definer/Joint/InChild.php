<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Joint;
use Hooloovoo\DatabaseMapping\ColumnFK;

/**
 * Class InChild
 */
class InChild extends AbstractJoint
{
    /**
     * @return int
     */
    public function getCardinality() : int
    {
        if (
            $this->getReferencingColumn()->getIsUnique() ||
            $this->getReferencingColumn()->getIsPrimaryKey()
        ) {
            return self::CARDINALITY_ONE;
        }

        return self::CARDINALITY_MANY;
    }

    /**
     * @return bool
     */
    public function isNullAble() : bool
    {
        return ($this->getCardinality() == self::CARDINALITY_ONE);
    }

    /**
     * @return string[]
     */
    public function getJoinClauses(): array
    {
        $parentEntityName = $this->_parent->getComponentEntityName();
        $parentFieldName = $this->_parent->getComponentTableMapping()->getSimplePrimaryKey()->getEntityFieldName();

        $childEntityName = $this->getChild()->getComponentTableMapping()->getEntityName();
        $childFieldName = $this->getReferencingColumn()->getEntityFieldName();

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
        return $this->_child->getComponentTableMapping()->getReferencingColumn(
            $this->_parent->getComponentTableMapping()->getName()
        );
    }
}