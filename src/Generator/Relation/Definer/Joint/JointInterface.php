<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer\Joint;

use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentInterface;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable;

/**
 * Interface JointInterface
 */
interface JointInterface
{
    const CARDINALITY_ONE = 1;
    const CARDINALITY_MANY = 2;

    /**
     * @return ComponentTable
     */
    public function getParent() : ComponentTable ;

    /**
     * @return ComponentInterface
     */
    public function getChild() : ComponentInterface ;

    /**
     * @return int
     */
    public function getCardinality() : int ;

    /**
     * @return bool
     */
    public function isNullAble() : bool ;

    /**
     * @return string[]
     */
    public function getJoinClauses(): array ;
}