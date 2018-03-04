<?php
namespace Hooloovoo\ORM\Relation\Manager;

use Hooloovoo\ORM\ComponentManagerInterface;
use Hooloovoo\ORM\Persistence\EntityManagerInterface;
use Hooloovoo\ORM\Relation\EQLQuery\EQLQueryInterface;

/**
 * Interface ManagerInterface
 */
interface ManagerInterface extends ComponentManagerInterface
{
    /**
     * @return string
     */
    public function getEntityName() : string ;

    /**
     * @return EntityManagerInterface
     */
    public function getParentManager() : EntityManagerInterface ;

    /**
     * @param string $queryString
     * @return EQLQueryInterface
     */
    public function getEQLQuery(string $queryString);

    /**
     * @param EQLQueryInterface $query
     * @return array
     */
    public function query(EQLQueryInterface $query) : array ;

    /**
     * @param int $primaryKey
     * @return mixed
     */
    public function getByPrimaryKey(int $primaryKey) ;

    /**
     * @param int[] $primaryKeys
     * @return mixed[]
     */
    public function getByPrimaryKeys(array $primaryKeys) : array ;

    /**
     * @param EQLQueryInterface $condition
     * @return mixed
     */
    public function getSingleByCondition(EQLQueryInterface $condition) ;

    /**
     * @param EQLQueryInterface $condition
     * @return mixed[]
     */
    public function getByCondition(EQLQueryInterface $condition) : array ;
}