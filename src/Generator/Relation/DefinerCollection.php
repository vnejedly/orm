<?php
namespace Hooloovoo\ORM\Generator\Relation;

use Hooloovoo\DatabaseMapping\Schema;
use Hooloovoo\ORM\Exception\LogicException;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentRelation;
use Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable;
use Hooloovoo\ORM\Generator\Relation\Definer\Definer;
use Hooloovoo\ORM\Generator\Relation\Definer\Joint\JointFactory;

/**
 * Class DefinerCollection
 */
class DefinerCollection
{
    /** @var Schema */
    protected $_schema;

    /** @var JointFactory */
    protected $_jointFactory;

    /** @var array */
    protected $_config;

    /** @var Definer[] */
    protected $_definers;

    /**
     * EntityManagersResolver constructor.
     * @param Schema $schema
     * @param JointFactory $jointFactory
     * @param array $config
     */
    public function __construct(Schema $schema, JointFactory $jointFactory, array $config)
    {
        $this->_schema = $schema;
        $this->_jointFactory = $jointFactory;
        $this->_config = $config;

        $this->initDefiners();
    }

    /**
     * @return Definer[]
     */
    public function getDefiners() : array
    {
        return $this->_definers;
    }

    /**
     * Definers initialization
     */
    protected function initDefiners()
    {
        foreach ($this->_config['entities'] as $entityName => $definition) {
            $this->_definers[$entityName] = new Definer(
                $entityName,
                new ComponentTable($this->_schema->getTable($definition['parent'])),
                $this->_jointFactory
            );
        }

        foreach ($this->_config['entities'] as $entityName => $definition) {
            $definer = $this->_definers[$entityName];

            foreach ($definition['children'] as $child) {
                $type = $child['type'];
                $name = $child['name'];

                if ($type == 'table') {
                    $component = new ComponentTable($this->_schema->getTable($name));
                } elseif ($type == 'relation') {
                    $component = new ComponentRelation($this->_definers[$name]);
                } else {
                    throw new LogicException("Unknown child type $type");
                }

                $definer->addChild($component);
            }
        }
    }
}