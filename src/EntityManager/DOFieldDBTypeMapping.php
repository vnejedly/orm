<?php
namespace Hooloovoo\ORM\EntityManager;

use Hooloovoo\Database\Database;
use Hooloovoo\DataObjects\Field\FieldBool;
use Hooloovoo\DataObjects\Field\FieldDateTime;
use Hooloovoo\DataObjects\Field\FieldFloat;
use Hooloovoo\DataObjects\Field\FieldInt;
use Hooloovoo\DataObjects\Field\FieldInterface;
use Hooloovoo\DataObjects\Field\FieldString;

/**
 * Class DOFieldsDBTypesMapping
 */
class DOFieldDBTypeMapping
{
    /**
     * @var array
     */
    protected $typeMapping = [
        FieldBool::TYPE => Database::PARAM_INT,
        FieldInt::TYPE => Database::PARAM_INT,
        FieldString::TYPE => Database::PARAM_STR,
        FieldFloat::TYPE => Database::PARAM_STR,
        FieldDateTime::TYPE => Database::PARAM_STR,
    ];

    /** @var FieldInterface */
    protected $field;

    /** @var array */
    protected $conversionArray;

    /**
     * DOFieldDBTypeMapping constructor.
     * @param FieldInterface $field
     */
    public function __construct(FieldInterface $field)
    {
        $this->field = $field;
        $this->conversionArray = $this->_getConversionArray();
    }

    /**
     * @return int
     */
    public function getInsertType() : int
    {
        $type = $this->field->getType();

        if (!array_key_exists($type, $this->typeMapping)) {
            return Database::PARAM_STR;
        }

        return $this->typeMapping[$type];
    }

    /**
     * @return mixed
     */
    public function getInsertValue()
    {
        if (is_null($this->field->getValue())) {
            return null;
        }

        $type = $this->field->getType();

        if (!array_key_exists($type, $this->conversionArray)) {
            return (string) $this->field->getValue();
        }

        return $this->conversionArray[$type]($this->field);
    }

    /**
     * @return array
     */
    protected function _getConversionArray() : array
    {
        return [
            FieldBool::TYPE => function (FieldBool $field) {
                return (bool) $field->getValue();
            },
            FieldInt::TYPE => function (FieldInt $field) {
                return (int) $field->getValue();
            },
            FieldString::TYPE => function (FieldString $field) {
                return (string) $field->getValue();
            },
            FieldFloat::TYPE => function (FieldFloat $field) {
                return (float) $field->getValue();
            },
            FieldDateTime::TYPE => function (FieldDateTime $value) {
                return (string) $value->getValue()->format('Y-m-d H:i:s');
            }
        ];
    }
}