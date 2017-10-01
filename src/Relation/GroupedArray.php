<?php
namespace Hooloovoo\ORM\Relation;

use Hooloovoo\ORM\ComponentManagerInterface;

/**
 * Class GroupedArray
 */
class GroupedArray
{
    /** @var array */
    protected $rawArray;

    /**
     * GroupedArray constructor.
     *
     * @param array $rawArray
     * @param ComponentManagerInterface[] $componentManagers
     */
    public function __construct(array $rawArray, array $componentManagers)
    {
        $this->setRawArray($rawArray, $componentManagers);
    }

    /**
     * @param array $rawArray
     * @param ComponentManagerInterface[] $componentManagers
     */
    protected function setRawArray(array $rawArray, array $componentManagers)
    {
        foreach ($componentManagers as $componentManager) {
            $tableName = $componentManager->getTableMapping()->getName();

            if (array_key_exists($tableName, $rawArray)) {
                $this->rawArray[$tableName] = array_values($rawArray[$tableName]);
            } else {
                $this->rawArray[$tableName] = [];
            }
        }
    }

    /**
     * @param string $tableName
     * @return mixed[]
     */
    public function getCollection(string $tableName) : array
    {
        return $this->rawArray[$tableName];
    }

    /**
     * @param string $tableName
     * @return mixed
     */
    public function getSingleComponent(string $tableName)
    {
        return $this->getFirstItemOrNullIfEmpty($this->rawArray[$tableName]);
    }

    /**
     * @param array $array
     * @return mixed
     */
    protected function getFirstItemOrNullIfEmpty(array $array)
    {
        if (array_key_exists(0, $array)) {
            return $array[0];
        }

        return null;
    }
}