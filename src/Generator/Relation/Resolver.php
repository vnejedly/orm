<?php
namespace Hooloovoo\ORM\Generator\Relation;

use Hooloovoo\Generator\ResolverInterface;
use Hooloovoo\ORM\Generator\Relation\Definer\Definer;
use Hooloovoo\ORM\Generator\Relation\Definer\FieldInfo;
use Generator;

/**
 * Class Resolver
 */
class Resolver implements ResolverInterface
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

    /**
     * @return Generator
     */
    public function yieldVariables() : Generator
    {
        foreach ($this->_definerCollection->getDefiners() as $definer) {
            echo "Generating relational layer {$definer->getName()}\n";
            yield $this->resolveVariables($definer);
        }
    }

    /**
     * @param Definer $definer
     * @return array
     */
    protected function resolveVariables(Definer $definer) : array
    {
        $hasCollections = false;
        $fields = $importEntities = [];
        foreach ($definer->getChildren() as $joint) {
            $field = new FieldInfo($joint);

            if ($field->isCollection()) {
                $hasCollections = true;
            }

            $fields[] = $field;
            if ($field->isPersistence()) {
                $importEntities[] = $field->getFieldEntityName();
            }
        }

        return [
            'fileName' => "{$definer->getName()}.php",
            'className' => $definer->getName(),
            'importEntities' => $importEntities,
            'parentComponent' => $definer->getParent(),
            'hasCollections' => $hasCollections,
            'fields' => $fields,
        ];
    }
}