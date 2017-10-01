<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

use Hooloovoo\DataObjects\Field\FieldDateTime;

/**
 * Class FieldDateTimeDef
 */
class FieldDateTimeDef extends FieldObjectDef
{
    const FIELD_CLASS_ALIAS = 'FieldDateTime';
    const VALUE_CLASS_ALIAS = 'DateTime';

    /**
     * FieldDateTimeDef constructor.
     */
    public function __construct()
    {
        $this->_setValueClass(FieldDateTime::TYPE, self::VALUE_CLASS_ALIAS);
        $this->_setFieldClass(FieldDateTime::class, self::FIELD_CLASS_ALIAS);
    }
}