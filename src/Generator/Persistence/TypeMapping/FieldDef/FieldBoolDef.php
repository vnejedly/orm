<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

use Hooloovoo\DataObjects\Field\FieldBool;

/**
 * Class EntityFieldBool
 */
class FieldBoolDef extends FieldScalarDef
{
    const FIELD_CLASS_ALIAS = 'FieldBool';

    /**
     * FieldBoolDef constructor.
     */
    public function __construct()
    {
        $this->_setHint(FieldBool::TYPE);
        $this->_setFieldClass(FieldBool::class, self::FIELD_CLASS_ALIAS);
    }
}