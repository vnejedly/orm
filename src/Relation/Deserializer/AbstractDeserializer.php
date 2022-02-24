<?php
namespace Hooloovoo\ORM\Relation\Deserializer;

use Hooloovoo\ORM\DeserializerInterface;

/**
 * Class AbstractDeserializer
 */
abstract class AbstractDeserializer implements DeserializerInterface
{
    /** @var DeserializerInterface */
    protected $parentDeserializer;

    /** @var DeserializerInterface[] */
    protected $deserializers = [];

    /**
     * @param DeserializerInterface $deserializer
     */
    public function setParentDeserializer(DeserializerInterface $deserializer)
    {
        $this->parentDeserializer = $deserializer;
    }

    /**
     * @param string $fieldName
     * @param DeserializerInterface $deserializer
     */
    public function addDeserializer(string $fieldName, DeserializerInterface $deserializer)
    {
        $this->deserializers[$fieldName] = $deserializer;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function deserializeParent(array $data)
    {
        return $this->parentDeserializer->deserialize($data);
    }

    /**
     * @param string $fieldName
     * @param array $data
     * @return mixed
     */
    protected function deserializeField(string $fieldName, array $data)
    {
        return $this->deserializers[$fieldName]->deserialize($data);
    }

    /**
     * @param string $fieldName
     * @param array $data
     * @return mixed[]
     */
    protected function deserializeCollection(string $fieldName, array $data) : array
    {
        $collection = [];
        foreach ($data as $index => $rawObject) {
            $collection[$index] = $this->deserializers[$fieldName]->deserialize($rawObject);
        }

        return $collection;
    }
}