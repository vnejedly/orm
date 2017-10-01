<?php
namespace Hooloovoo\ORM\Generator\Relation;

use Hooloovoo\Generator\ResolverInterface;

/**
 * Class AbstractResolver
 */
abstract class AbstractResolver implements ResolverInterface
{
    /** @var DefinerCollection */
    protected $_definerCollection;

    /**
     * AbstractResolver constructor.
     *
     * @param DefinerCollection $definerCollection
     */
    public function __construct(DefinerCollection $definerCollection)
    {
        $this->_definerCollection = $definerCollection;
    }
}