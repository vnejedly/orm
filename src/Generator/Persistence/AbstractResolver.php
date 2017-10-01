<?php
namespace Hooloovoo\ORM\Generator\Persistence;

use Hooloovoo\DatabaseMapping\Schema;
use Hooloovoo\Generator\ResolverInterface;
use Hooloovoo\ORM\Generator\Persistence\TypeMapping\Mapping;

/**
 * Class AbstractResolver
 */
abstract class AbstractResolver implements ResolverInterface
{
    /** @var Schema */
    protected $_schema;

    /** @var Mapping */
    protected $_mapping;

    /**
     * EntityManagersResolver constructor.
     * @param Schema $schema
     * @param Mapping $mapping
     */
    public function __construct(Schema $schema, Mapping $mapping)
    {
        $this->_schema = $schema;
        $this->_mapping = $mapping;
    }

    /**
     * @param array $main
     * @param array $additional
     */
    protected function _arrayAdd(array &$main, array $additional)
    {
        foreach ($additional as $item) {
            if (!in_array($item, $main)) {
                $main[] = $item;
            }
        }
    }
}