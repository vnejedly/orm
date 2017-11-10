<?php
namespace Hooloovoo\ORM\Exception;

use Throwable;

/**
 * Interface HttpExceptionInterface
 */
interface HttpExceptionInterface extends Throwable
{
    /**
     * @return int
     */
    public function getStatusCode() : int ;
}