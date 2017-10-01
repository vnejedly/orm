<?php
namespace Hooloovoo\ORM\Relation\EQLQuery;

use Hooloovoo\Database\Query\QueryInterface;
use Hooloovoo\DatabaseMapping\Table;

/**
 * Class AbstractEQLQuery
 */
interface EQLQueryInterface extends QueryInterface
{
    /**
     * @param Table $tableMapping
     * @param bool $parent
     */
    public function addComponentTable(Table $tableMapping, bool $parent = false) ;

    /**
     * @param EQLQueryInterface $EQLQuery
     */
    public function addComponentEQLQuery(EQLQueryInterface $EQLQuery) ;

    /**
     * @return Table
     */
    public function getParentComponentTable() : Table ;

    /**
     * @return Table[]
     */
    public function getComponentTables() : array ;

    /**
     * @return Table[]
     */
    public function getComponentParentTables() : array ;

    /**
     * @return string
     */
    public function getAllSelectionColumns() : string ;
}