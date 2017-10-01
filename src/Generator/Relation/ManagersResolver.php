<?php
namespace Hooloovoo\ORM\Generator\Relation;

use Hooloovoo\ORM\Generator\Relation\Definer\Definer;
use Hooloovoo\ORM\Generator\Relation\Definer\FieldInfo;
use Generator;

/**
 * Class ManagersResolver
 * @package Hooloovoo\ORM\Generator\Relation
 */
class ManagersResolver extends AbstractResolver
{
    /**
     * @return Generator
     */
    public function yieldVariables() : Generator
    {
        foreach ($this->_definerCollection->getDefiners() as $definer) {
            echo "Generating relational manager {$definer->getName()}\n";
            yield $this->resolveVariables($definer);
        }
    }

    /**
     * @param Definer $definer
     * @return array
     */
    protected function resolveVariables(Definer $definer) : array
    {
        $fields = $importManagers = [];
        foreach ($definer->getChildren() as $joint) {
            $field = new FieldInfo($joint);
            $fields[] = $field;
        }

        return [
            'fileName' => "{$definer->getName()}.php",
            'className' => $definer->getName(),
            'fields' => $fields,
            'parentComponent' => $definer->getParent(),
        ];
    }
}