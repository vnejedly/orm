<?php
namespace Hooloovoo\ORM\Generator\Persistence;

use Hooloovoo\DatabaseMapping\Column;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldDefInterface;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldObjectDef;

/**
 * Class FieldInfo
 */
class FieldInfo
{
    /** @var string */
    protected $_name;

    /** @var FieldDefInterface */
    protected $_fieldDef;

    /** @var Column */
    protected $_column;

    /** @var bool */
    protected $_nullAble = false;

    /**
     * FieldInfo constructor.
     *
     * @param FieldDefInterface $fieldDef
     * @param Column $column
     */
    public function __construct(FieldDefInterface $fieldDef, Column $column)
    {
        $this->_fieldDef = $fieldDef;
        $this->_column = $column;

        $this->_name = $column->getEntityFieldName();
        $this->_nullAble = $column->getIsNullAble() || $column->getIsAutoIncrement();
    }

    /**
     * @return bool
     */
    public function isValueObject() : bool
    {
        return ($this->_fieldDef instanceof FieldObjectDef);
    }

    /**
     * @return string
     */
    public function getValueClassAlias() : string
    {
        if (!$this->_fieldDef instanceof FieldObjectDef) {
            return null;
        }

        return $this->_fieldDef->getValueClassAlias();
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getAnnotation() : string
    {
        return $this->_fieldDef->getAnnotation($this->_name);
    }

    /**
     * @return string
     */
    public function getDeclaration() : string
    {
        $fieldName = $this->_fieldDef->getDeclaration($this->_name);
        return "$fieldName = null";
    }

    /**
     * @return array
     */
    public function getImports() : array
    {
        return $this->_fieldDef->getImports();
    }

    /**
     * @return string
     */
    public function getInstanceCreation() : string
    {
        return $this->_fieldDef->getInstanceCreation($this->_name);
    }

    /**
     * @return FieldDefInterface
     */
    public function getFieldDef() : FieldDefInterface
    {
        return $this->_fieldDef;
    }

    /**
     * @return Column
     */
    public function getColumn() : Column
    {
        return $this->_column;
    }
}