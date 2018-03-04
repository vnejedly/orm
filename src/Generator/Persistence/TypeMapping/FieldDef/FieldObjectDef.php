<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

/**
 * Class FieldObjectDef
 */
abstract class FieldObjectDef extends AbstractFieldDef
{
    /** @var string */
    protected $_valueClassName;

    /** @var string */
    protected $_valueClassAlias;

    /**
     * EntityFieldObject constructor.
     *
     * @param string $className
     * @param string $alias
     */
    protected function _setValueClass(string $className, string $alias)
    {
        $this->_valueClassName = $className;
        $this->_valueClassAlias = $alias;
        $this->_hint = $alias;
        $this->_addImport($className, $alias);
    }

    public function getValueClassImport() : string
    {
        return "{$this->_valueClassName} as {$this->_valueClassAlias}";
    }

    /**
     * @return string
     */
    public function getValueClassName(): string
    {
        return $this->_valueClassName;
    }

    /**
     * @return string
     */
    public function getValueClassAlias(): string
    {
        return $this->_valueClassAlias;
    }
}