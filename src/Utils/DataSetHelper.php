<?php
namespace Hooloovoo\ORM\Utils;

/**
 * Class DataSetHelper
 */
class DataSetHelper
{
    /** @var mixed[][] */
    protected $data;

    /**
     * ArrayHelpers constructor.
     * @param mixed[][] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $columnName
     * @param bool $unique
     * @param bool $notNull
     * @return mixed[]
     */
    public function getColumnValues(string $columnName, bool $unique = false, bool $notNull = false) : array
    {
        $columnValues = [];
        foreach ($this->data as $row) {
            if (!$notNull || !is_null($row[$columnName])) {
                $columnValues[] = $row[$columnName];
            }
        }

        if ($unique) {
            return array_unique($columnValues);
        }

        return $columnValues;
    }
}