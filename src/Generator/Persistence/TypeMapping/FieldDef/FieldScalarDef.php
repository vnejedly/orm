<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef;

/**
 * Class FieldScalarDef
 */
abstract class FieldScalarDef extends AbstractFieldDef
{
    /**
     * @param string $hint
     */
    protected function _setHint(string $hint)
    {
        $this->_hint = $hint;
    }
}