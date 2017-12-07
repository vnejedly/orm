<?php
namespace Hooloovoo\ORM\Utils;

/**
 * Class ArrayPager
 */
class ArrayPager
{
    /** @var array */
    protected $array;

    /**
     * ArrayPager constructor.
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * @return int
     */
    public function getTotalCount() : int
    {
        return count($this->array);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param bool $preserveKeys
     * @return array
     */
    public function getPage(int $offset, int $limit, bool $preserveKeys = false) : array
    {
        $index = 0;
        $result = [];
        foreach ($this->array as $key => $row) {
            if ($index >= $offset + $limit) {
                break;
            }

            if ($index >= $offset) {
                if ($preserveKeys) {
                    $result[$key] = $row;
                } else {
                    $result[] = $row;
                }
            }

            $index++;
        }

        return $result;
    }
}