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

    /** @var Param[] */
    protected $params = [];

    /** @var MultiParam[] */
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
     * @param string $name
     * @param $value
     * @param int $type
     */
    public function addParam(string $name, $value, int $type)
    {
        $this->params[$name] = new Param($type, $value);
    }

    /**
     * @param string $name
     * @param mixed[] $values
     * @param int $type
     */
    public function addMultiParam(string $name, array $values, int $type)
    {
        $this->multiParams[$name] = new MultiParam($name, $type, $values);
    }

    /**
     * @param EQLQueryInterface $eqlQuery
     */
    public function parametrizeQuery(EQLQueryInterface $eqlQuery)
    {
        foreach ($this->params as $name => $param) {
            $eqlQuery->addParam($name, $param->getValue(), $param->getType());
        }

        foreach ($this->multiParams as $name => $multiParam) {
            $eqlQuery->addParam($name, $multiParam->getValue(), $multiParam->getType());
        }
    }
}