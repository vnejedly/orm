<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

use Hooloovoo\DataObjects\Field\FieldFloat;

/**
 * Class FieldFloatDef
 */
class FieldFloatDef extends FieldScalarDef
{
    const FIELD_CLASS_ALIAS = 'FieldFloat';

    /**
     * FieldFloatDef constructor.
     */
    public function __construct()
    {
        $this->_setHint(FieldFloat::TYPE);
        $this->_setFieldClass(FieldFloat::class, self::FIELD_CLASS_ALIAS);
    }
}