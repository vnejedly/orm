<?php
namespace Hooloovoo\ORM\Relation\Restrictor;

use Hooloovoo\Database\Query\Param\MultiParam;
use Hooloovoo\Database\Query\Param\Param;
use Hooloovoo\ORM\Relation\EQLQuery\EQLQueryInterface;

/**
 * Class Restrictor
 */
class Restrictor
{
    /** @var string[] */
    protected $restrictions = [];

    /** @var Param[][] */
    protected $params = [];

    /** @var MultiParam[][] */
    protected $multiParams = [];

    /**
     * @param string $componentName
     * @param string $restriction
     */
    public function addRestriction(string $componentName, string $restriction)
    {
        $this->restrictions[$componentName] = $restriction;
    }

    /**
     * @param string $componentName
     * @return string
     */
    public function getRestriction(string $componentName) : string
    {
        if (!array_key_exists($componentName, $this->restrictions)) {
            return '';
        }

        return "AND {$this->restrictions[$componentName]}";
    }

    /**
     * @param string $componentName
     * @param string $name
     * @param $value
     * @param int $type
     */
    public function addParam(string $componentName, string $name, $value, int $type)
    {
        $this->params[$componentName][$name] = new Param($type, $value);
    }

    /**
     * @param string $componentName
     * @param string $name
     * @param array $values
     * @param int $type
     */
    public function addMultiParam(string $componentName, string $name, array $values, int $type)
    {
        $this->multiParams[$componentName][$name] = new MultiParam($name, $type, $values);
    }

    /**
     * @param EQLQueryInterface $eqlQuery
     * @param string[] $componentNames
     */
    public function parametrizeQuery(EQLQueryInterface $eqlQuery, array $componentNames)
    {
        foreach ($componentNames as $componentName) {
            if (array_key_exists($componentName, $this->params)) {
                foreach ($this->params[$componentName] as $name => $param) {
                    $eqlQuery->addParam($name, $param->getValue(), $param->getType());
                }
            }

            if (array_key_exists($componentName, $this->multiParams)) {
                foreach ($this->multiParams[$componentName] as $name => $multiParam) {
                    $eqlQuery->addMultiParam($name, $multiParam->getValues(), $multiParam->getType());
                }
            }
        }
    }
}