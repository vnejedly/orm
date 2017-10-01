<?php
namespace Hooloovoo\ORM\Generator\Persistence\TypeMapping;

use Hooloovoo\ORM\Exception\LogicException;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldBoolDef;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldDateTimeDef;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldDefInterface;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldFloatDef;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldIntDef;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\FieldDef\FieldStringDef;

/**
 * Class Mapping
 */
class Mapping
{
    /** @var FieldDefInterface[] */
    protected $_mapping;

    /**
     * Mapping constructor.
     */
    public function __construct()
    {
        $this->_mapping = [
            MysqlDataTypes::TINYINT => new FieldBoolDef(),
            MysqlDataTypes::INT => new FieldIntDef(),
            MysqlDataTypes::SMALLINT => new FieldIntDef(),
            MysqlDataTypes::MEDIUMINT => new FieldIntDef(),
            MysqlDataTypes::BIGINT => new FieldIntDef(),
            MysqlDataTypes::FLOAT => new FieldFloatDef(),
            MysqlDataTypes::DOUBLE => new FieldFloatDef(),
            MysqlDataTypes::DECIMAL => new FieldFloatDef(),
            MysqlDataTypes::DATE => new FieldDateTimeDef(),
            MysqlDataTypes::DATETIME => new FieldDateTimeDef(),
            MysqlDataTypes::TIMESTAMP => new FieldDateTimeDef(),
            MysqlDataTypes::TIME => new FieldDateTimeDef(),
            MysqlDataTypes::YEAR => new FieldDateTimeDef(),
            MysqlDataTypes::CHAR => new FieldStringDef(),
            MysqlDataTypes::VARCHAR => new FieldStringDef(),
            MysqlDataTypes::BLOB => new FieldStringDef(),
            MysqlDataTypes::TEXT => new FieldStringDef(),
            MysqlDataTypes::TINYBLOB => new FieldStringDef(),
            MysqlDataTypes::TINYTEXT => new FieldStringDef(),
            MysqlDataTypes::MEDIUMBLOB => new FieldStringDef(),
            MysqlDataTypes::MEDIUMTEXT => new FieldStringDef(),
            MysqlDataTypes::LONGBLOB => new FieldStringDef(),
            MysqlDataTypes::LONGTEXT => new FieldStringDef(),
            MysqlDataTypes::ENUM => new FieldStringDef(),
            MysqlDataTypes::JSON => new FieldStringDef(),
        ];
    }

    /**
     * @param string $databaseType
     * @return FieldDefInterface
     */
    public function getFieldDefinition(string $databaseType)
    {
        $key = strtoupper($databaseType);

        if (!array_key_exists($key, $this->_mapping)) {
            throw new LogicException("Unknown data type ($databaseType)");
        }

        return $this->_mapping[$key];
    }
}