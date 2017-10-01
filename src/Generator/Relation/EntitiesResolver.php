<?php
namespace Hooloovoo\ORM\Generator\Relation;

use Hooloovoo\ORM\Generator\Relation\Definer\Definer;
use Hooloovoo\ORM\Generator\Relation\Definer\FieldInfo;
use Generator;

/**
 * Class EntitiesResolver
 */
class EntitiesResolver extends AbstractResolver
{
    /**
     * @return Generator
     */
    public function yieldVariables() : Generator
    {
        foreach ($this->_definerCollection->getDefiners() as $definer) {
            echo "Generating relational entity {$definer->getName()}\n";
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
            'fields' => $fields,
            'importEntities' => $importEntities,
            'parentComponent' => $definer->getParent(),
            'hasCollections' => $hasCollections
        ];
    }
}