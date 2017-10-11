<?php
namespace Hooloovoo\ORM\Exception;

use Throwable;

/**
 * Class NonOriginalEntityException
 */
class NonOriginalEntityException extends LogicException
{
    /**
     * NonOriginalEntityException constructor.
     *
     * @param string $entityName
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $entityName, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Entity for update ($entityName) must have an original id from database", $code, $previous);
    }
}