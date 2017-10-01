<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

/**
 * Interface FieldDefInterface
 */
interface FieldDefInterface
{
    /**
     * @return array
     */
    public function getImports() : array ;

    /**
     * @param string $name
     * @return string
     */
    public function getAnnotation($name) : string ;

    /**
     * @param string $name
     * @return string
     */
    public function getDeclaration(string $name) : string ;

    /**
     * @param string $name
     * @return string
     */
    public function getInstanceCreation(string $name) : string ;
}