<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $entityManagerNamespace
 * @var string $tableDescriptorNamespace
 * @var string $entityNamespace
 * @var string $managerName
 * @var string $tableDescriptorName
 * @var string $entityName
 * @var \Hooloovoo\ORM\Generator\Persistence\FieldInfo[] $fields
 * @var \Hooloovoo\ORM\Generator\Persistence\FieldInfo[] $nonPKFields
 * @var string[] $imports
 * @var string[] $valueClassImports
 */ ?>
namespace <?= $entityManagerNamespace ?>;

use Hooloovoo\Database\Database;
use Hooloovoo\ORM\Persistence\EQLQuery;
use Hooloovoo\ORM\Persistence\AbstractEntityManager;
use Hooloovoo\ORM\Persistence\QueryEngineConnector;
use <?= $entityNamespace ?>\<?= $entityName ?> as Entity;
use <?= $tableDescriptorNamespace ?>\<?= $tableDescriptorName ?> as Descriptor;
use Hooloovoo\ORM\EventDispatcher\ConnectorInterface as DispatcherConnector;
use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\QueryEngine\Query\Query;
<?php foreach ($valueClassImports as $import): ?>
use <?= $import ?>;
<?php endforeach; ?>

/**
 * Class <?= $managerName ?>
 *
 * +-------------------------------------------------------+
 * ! this class is auto-generated, please do not change it !
 * +-------------------------------------------------------+
 *
 * <?= $generatedDateTime->format('c') ?>
 */
class <?= $managerName ?> extends AbstractEntityManager
{
    <?php foreach ($fields as $field): ?>
    const <?= $field->getFieldConstantName() ?> = '<?= $field->getName() ?>';
    <?php endforeach; ?>

    <?php foreach ($fields as $field): ?>
    const <?= $field->getColumnConstantName() ?> = '<?= $field->getColumnName() ?>';
    <?php endforeach; ?>

    const EVENT_CREATE_BEFORE = self::EVENT_PREFIX_CREATE_BEFORE . '.<?= strtolower($managerName) ?>';
    const EVENT_CREATE_AFTER = self::EVENT_PREFIX_CREATE_AFTER . '.<?= strtolower($managerName) ?>';
    const EVENT_UPDATE_BEFORE = self::EVENT_PREFIX_UPDATE_BEFORE . '.<?= strtolower($managerName) ?>';
    const EVENT_UPDATE_AFTER = self::EVENT_PREFIX_UPDATE_AFTER . '.<?= strtolower($managerName) ?>';
    const EVENT_DELETE_BEFORE = self::EVENT_PREFIX_DELETE_BEFORE . '.<?= strtolower($managerName) ?>';
    const EVENT_DELETE_AFTER = self::EVENT_PREFIX_DELETE_AFTER . '.<?= strtolower($managerName) ?>';

    /**
     * <?= $managerName ?> constructor.
     *
     * @param Database $database
     * @param DispatcherConnector $dispatcherConnector
     * @param Descriptor $tableDescriptor
     */
    public function __construct(
        Database $database,
        DispatcherConnector $dispatcherConnector,
        Descriptor $tableDescriptor
    ) {
        $this->setDatabase($database);
        $this->setDispatcherConnector($dispatcherConnector);
        $this->resolveMapping($tableDescriptor);
    }

    /**
     * @param array $data
     * @return DataObjectInterface
     */
    protected function getEntityFromRow(array $data) : DataObjectInterface
    {
        return new Entity(
        <?php foreach ($fields as $field): ?>
            <?php if ($field->isValueObject()): ?>
            new <?= $field->getValueClassAlias() ?>($data['<?= $field->getColumn()->getColumnName() ?>'])<?= $this->_delimit($fields, ',') ?>
            <?php else: ?>
            $data['<?= $field->getColumn()->getColumnName() ?>']<?= $this->_delimit($fields, ',') ?>
            <?php endif; ?>
        <?php endforeach; ?>
        );
    }

    /**
     * @param int $primaryKey
     * @return Entity
     */
    public function getByPrimaryKey(int $primaryKey) : Entity
    {
        return $this->_getByPrimaryKey($primaryKey);
    }

    /**
     * @param int[] $primaryKeys
     * @return Entity[]
     */
    public function getByPrimaryKeys(array $primaryKeys) : array
    {
        return $this->_getByPrimaryKeys($primaryKeys);
    }

    /**
     * @param EQLQuery $conditionQuery
     * @return Entity[]
     */
    public function getObjects(EQLQuery $conditionQuery) : array
    {
        return $this->_getObjects($conditionQuery);
    }

    /**
     * @param EQLQuery $conditionQuery
     * @return Entity
     */
    public function getObject(EQLQuery $conditionQuery) : Entity
    {
        return $this->_getObject($conditionQuery);
    }

    /**
     * @param Query $query
     * @param int $totalCount
     * @return Entity[]
     */
    public function getByQueryEngine(Query $query, int &$totalCount = null) : array
    {
        $totalCount = $this->_getCountByQueryEngine($query, new QueryEngineConnector(
            $this->getEQLQuery('SELECT count(*) AS cnt FROM {@} WHERE'))
        );

        return $this->_getByQueryEngine($query, new QueryEngineConnector(
            $this->getEQLQuery('SELECT {*} FROM {@} WHERE'))
        );
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @param int $type
     * @return Entity
     */
    public function getObjectByField(string $fieldName, $value, int $type = Database::PARAM_STR) : Entity
    {
        return $this->_getObjectByField($fieldName, $value, $type);
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @param int $type
     * @return Entity[]
     */
    public function getObjectsByField(string $fieldName, $value, int $type = Database::PARAM_STR) : array
    {
        return $this->_getObjectsByField($fieldName, $value, $type);
    }

    /**
     * @param array $fieldSet
     * @return Entity
     */
    public function getObjectByFieldSet(array $fieldSet) : Entity
    {
        return $this->_getObjectByFieldSet($fieldSet);
    }

    /**
     * @param array $fieldSet
     * @return Entity[]
     */
    public function getObjectsByFieldSet(array $fieldSet) : array
    {
        return $this->_getObjectsByFieldSet($fieldSet);
    }

    /**
     * @param array $fieldValues
     * @param bool $returnObject
     * @return Entity
     */
    public function create(array $fieldValues, bool $returnObject = true)
    {
        return $this->_create($fieldValues, $returnObject);
    }

    /**
     * @param Entity $entity
     * @param bool $returnObject
     * @return Entity
     */
    public function createByEntity(Entity $entity, bool $returnObject = true)
    {
        return $this->_createByEntity($entity, $returnObject);
    }

    /**
     * @param int $primaryKey
     * @param Entity $entity
     * @param bool $returnObject
     * @return Entity
     */
    public function replace(int $primaryKey, Entity $entity, bool $returnObject = true)
    {
        return $this->_replace($primaryKey, $entity, $returnObject);
    }

    /**
     * @param int $primaryKey
     * @param array $fieldValues
     * @param bool $returnObject
     * @return Entity
     */
    public function update(int $primaryKey, array $fieldValues, bool $returnObject = true)
    {
        return $this->_update($primaryKey, $fieldValues, $returnObject);
    }

    /**
     * @param Entity $entity
     * @param bool $returnObject
     * @return Entity
     */
    public function updateByEntity(Entity $entity, bool $returnObject = true)
    {
        return $this->_updateByEntity($entity, $returnObject);
    }

    /**
     * @param array $resultSet
     * @return Entity[]
     */
    public function getCollectionFromPrefixedResultSet(array $resultSet) : array
    {
        return $this->_getCollectionFromPrefixedResultSet($resultSet);
    }

    /**
     * @return Entity
     */
    public function getNewEntity()
    {
        return new Entity();
    }
}