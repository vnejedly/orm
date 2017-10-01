<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

/**
 * Class AbstractFieldDef
 */
abstract class AbstractFieldDef implements FieldDefInterface
{
    /** @var string[] */
    protected $_imports = [];

    /** @var string */
    protected $_hint;

    /** @var string */
    protected $_fieldClass;

    /** @var string */
    protected $_fieldClassAlias;

    /**
     * @return string[]
     */
    public function getImports() : array
    {
        return $this->_imports;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getAnnotation($name) : string
    {
        return "{$this->_hint} \${$name}";
    }

    /**
     * @param string $name
     * @return string
     */
    public function getDeclaration(string $name) : string
    {
        return "{$this->_hint} \${$name}";
    }

    /**
     * @param string $name
     * @return string
     */
    public function getInstanceCreation(string $name) : string
    {
        return "{$this->_fieldClassAlias}(\${$name})";
    }

    /**
     * @param string $className
     * @param string $alias
     */
    protected function _setFieldClass(string $className, string $alias)
    {
        $this->_fieldClass = $className;
        $this->_fieldClassAlias =  $alias;
        $this->_addImport($className, $alias);
    }

    /**
     * @param $className
     * @param string $alias
     */
    protected function _addImport(string $className, string $alias)
    {
        $this->_imports[] = "$className as $alias";
    }
}