<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Joint;

use Hooloovoo\DatabaseMapping\ColumnFK;
use Hooloovoo\DatabaseMapping\Table;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentInterface;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable;

/**
 * Class InTable
 */
class InTable extends AbstractJoint
{
    /** @var Table */
    protected $_mapTable;

    /**
     * InTable constructor.
     *
     * @param ComponentTable $parent
     * @param ComponentInterface $child
     * @param Table $mapTable
     */
    public function __construct(
        ComponentTable $parent,
        ComponentInterface $child,
        Table $mapTable
    ) {
        parent::__construct($parent, $child);
        $this->_mapTable = $mapTable;
    }

    /**
     * @return int
     */
    public function getCardinality() : int
    {
        if ($this->getChildReferencingColumn()->getIsUnique()) {
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
        $mapTableName = $this->_mapTable->getName();

        $parentEntityName = $this->_parent->getComponentEntityName();
        $childEntityName = $this->_child->getComponentTableMapping()->getEntityName();

        $parentRefColumnStr = $mapTableName . '.' . $this->getParentReferencingColumn()->getColumnName();
        $childRefColumnStr = $mapTableName . '.' . $this->getChildReferencingColumn()->getColumnName();

        $parentPKFieldName = $this->_parent->getComponentTableMapping()->getSimplePrimaryKey()->getEntityFieldName();
        $childPKFieldName = $this->_child->getComponentTableMapping()->getSimplePrimaryKey()->getEntityFieldName();

        $childEntityStr = '{' . $childEntityName . '}';
        $parentFieldStr = '{' . $parentEntityName . '.' . $parentPKFieldName . '}';
        $childFieldStr = '{' . $childEntityName . '.' . $childPKFieldName . '}';

        return [
            "LEFT JOIN $mapTableName ON $parentFieldStr = $parentRefColumnStr",
            "LEFT JOIN $childEntityStr ON $childRefColumnStr = $childFieldStr",
        ];
    }

    /**
     * @return ColumnFK
     */
    protected function getChildReferencingColumn() : ColumnFK
    {
        return $this->_mapTable->getReferencingColumn($this->_child->getComponentTableMapping()->getName());
    }

    /**
     * @return ColumnFK
     */
    protected function getParentReferencingColumn() : ColumnFK
    {
        return $this->_mapTable->getReferencingColumn($this->_parent->getComponentTableMapping()->getName());
    }
}