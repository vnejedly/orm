<?php
namespace Hooloovoo\ORM\EntityManager;

use Hooloovoo\Database\Database;
use Hooloovoo\DatabaseMapping\Table as TableMapping;
use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\ORM\ComponentManagerInterface;

/**
 * Class EntityManagerInterface
 */
interface EntityManagerInterface extends ComponentManagerInterface
{
    /**
     * @param array $resultSet
     * @return DataObjectInterface[]
     */
    public function getCollectionFromPrefixedResultSet(array $resultSet) : array ;

    /**
     * @param string $conditionString
     * @return EQLQuery
     */
    public function getEQLQuery(string $conditionString = '') : EQLQuery ;

    /**
     * @return TableMapping
     */
    public function getTableMapping() : TableMapping ;

    /**
     * @return Database
     */
    public function getDatabase() : Database ;

    /**
     * @param EQLQuery $query
     * @return int
     */
    public function getCount(EQLQuery $query) : int ;

    /**
     * @param int $primaryKey
     */
    public function delete(int $primaryKey) ;
}