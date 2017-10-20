<?php
namespace Hooloovoo\ORM\Generator\Relation\Definer;

use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable;
use Hooloovoo\ORM\Generator\Relation\Definer\Joint\JointInterface;

/**
 * Class FieldInfo
 */
class FieldInfo
{
    /** @var JointInterface */
    protected $_joint;

    /**
     * FieldInfo constructor.
     *
     * @param JointInterface $joint
     */
    public function __construct(JointInterface $joint)
    {
        $this->_joint = $joint;
    }

    /**
     * @return bool
     */
    public function isPersistence(): bool
    {
        return ($this->_joint->getChild() instanceof ComponentTable);
    }

    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return ($this->_joint->getCardinality() == JointInterface::CARDINALITY_MANY);
    }

    /**
     * @return string
     */
    public function getFieldEntityName(): string
    {
        return $this->_joint->getChild()->getComponentEntityName();
    }

    /**
     * @return string
     */
    public function getFieldTableName(): string
    {
        return $this->_joint->getChild()->getComponentTableMapping()->getName();
    }

    /**
     * @return string
     */
    public function getFieldEntityManagerAlias(): string
    {
        if ($this->isPersistence()) {
            $suffix = 'PM';
        } else {
            $suffix = 'RM';
        }

        return $this->getFieldEntityName() . $suffix;
    }

    /**
     * @return string
     */
    public function getFieldEntityManagerVariableName(): string
    {
        return lcfirst($this->getFieldEntityManagerAlias());
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->_joint->getChild()->getComponentFieldName();
    }

    /**
     * @return string
     */
    public function getDeclaration(): string
    {
        if ($this->_joint->getCardinality() == JointInterface::CARDINALITY_ONE) {
            $hint = $this->_joint->getChild()->getComponentEntityName();
        } else {
            $hint = 'array';
        }

        $default = '';
        if ($this->_joint->isNullAble()) {
            $default = ' = null';
        }

        return "$hint \${$this->getFieldName()}{$default}";
    }

    /**
     * @return string
     */
    public function getAnnotation(): string
    {
        $hint = $this->_joint->getChild()->getComponentEntityName();

        if ($this->_joint->getCardinality() == JointInterface::CARDINALITY_MANY) {
            $hint = "{$hint}[]";
        }

        return "$hint \${$this->getFieldName()}";
    }

    /**
     * @return string
     */
    public function getFieldClass(): string
    {
        if ($this->_joint->getCardinality() == JointInterface::CARDINALITY_ONE) {
            return 'FieldDataObject';
        }

        return 'FieldCollection';
    }

    /**
     * @return string[]
     */
    public function getJoinClauses(): array
    {
        return $this->_joint->getJoinClauses();
    }

    /**
     * @return bool
     */
    public function isCardinalityOne() : bool
    {
        return $this->_joint->getCardinality() == JointInterface::CARDINALITY_ONE;
    }
}