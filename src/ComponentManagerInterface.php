<?php
namespace Hooloovoo\ORM;

use Hooloovoo\DatabaseMapping\Table;
use Hooloovoo\DataObjects\DataObjectInterface;

/**
 * Interface ComponentManagerInterface
 */
interface ComponentManagerInterface
{
    /**
     * @return Table
     */
    public function getTableMapping() : Table ;

    /**
     * @param int[] $primaryKeys
     * @return DataObjectInterface[]
     */
    public function getByPrimaryKeys(array $primaryKeys) : array ;
}