<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

use Hooloovoo\DataObjects\Field\FieldString;

/**
 * Class FieldStringDef
 */
class FieldStringDef extends FieldScalarDef
{
    const FIELD_CLASS_ALIAS = 'FieldString';

    /**
     * FieldStringDef constructor.
     */
    public function __construct()
    {
        $this->_setHint(FieldString::TYPE);
        $this->_setFieldClass(FieldString::class, self::FIELD_CLASS_ALIAS);
    }
}