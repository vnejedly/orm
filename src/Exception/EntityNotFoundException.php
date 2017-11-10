<?php
namespace Hooloovoo\ORM\Exception;

/**
 * Class EntityNotFoundException
 */
class EntityNotFoundException extends RuntimeException implements HttpExceptionInterface
{
    const HTTP_CODE = 404;

    /** @var string */
    protected $_entityName;

    /**
     * EntityNotFoundException constructor.
     *
     * @param string $entityName
     * @param Exception $previous
     */
    public function __construct(
        string $entityName,
        Exception $previous = null
    ) {
        $this->_entityName = $entityName;
        parent::__construct("Entity not found ($entityName)", self::HTTP_CODE, $previous);
    }

    /**
     * @return string
     */
    public function getEntityName() : string
    {
        return $this->_entityName;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return self::HTTP_CODE;
    }
}