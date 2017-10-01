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
     * @return mixed[]
     */
    public function getColumnValues(string $columnName) : array
    {
        $columnValues = [];
        foreach ($this->data as $row) {
            $columnValues[] = $row[$columnName];
        }

        return $columnValues;
    }

    /**
     * @param string $columnName
     * @return array
     */
    public function getColumnValuesUnique(string $columnName) : array
    {
        return array_unique($this->getColumnValues($columnName));
    }
}