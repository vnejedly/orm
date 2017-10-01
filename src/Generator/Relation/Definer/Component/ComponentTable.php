<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Component;

use Hooloovoo\DatabaseMapping\Table;

/**
 * Class ComponentTable
 */
class ComponentTable extends AbstractComponent
{
    /** @var Table */
    protected $_tableMapping;

    /**
     * ComponentTable constructor.
     *
     * @param Table $tableMapping
     */
    public function __construct(Table $tableMapping)
    {
        $this->_tableMapping = $tableMapping;
    }

    /**
     * @return string
     */
    public function getComponentEntityName() : string
    {
        return $this->_tableMapping->getEntityName();
    }

    /**
     * @return Table
     */
    public function getComponentTableMapping() : Table
    {
        return $this->_tableMapping;
    }
}