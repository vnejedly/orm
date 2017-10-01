<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

use Hooloovoo\DataObjects\Field\FieldInt;

/**
 * Class FieldIntDef
 */
class FieldIntDef extends FieldScalarDef
{
    const FIELD_CLASS_ALIAS = 'FieldInt';

    /**
     * FieldIntDef constructor.
     */
    public function __construct()
    {
        $this->_setHint(FieldInt::TYPE);
        $this->_setFieldClass(FieldInt::class, self::FIELD_CLASS_ALIAS);
    }
}