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
 */ ?>
namespace <?= $entityManagerNamespace ?>;

use Hooloovoo\Database\Database;
use Hooloovoo\ORM\EntityManager\EQLQuery;
use Hooloovoo\ORM\EntityManager\AbstractEntityManager;
use <?= $entityNamespace ?>\<?= $entityName ?> as Entity;
use <?= $tableDescriptorNamespace ?>\<?= $tableDescriptorName ?> as Descriptor;
use Hooloovoo\DataObjects\DataObjectInterface;
use Hooloovoo\QueryEngine\Query\Query;
<?php foreach ($imports as $import): ?>
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
    /**
     * <?= $managerName ?> constructor.
     *
     * @param Database $database
     * @param Descriptor $tableDescriptor
     */
    public function __construct(
        Database $database,
        Descriptor $tableDescriptor
    ) {
        $this->setDatabase($database);
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
     * @param EQLQuery $condition
     * @return Entity[]
     */
    public function getByQueryEngine(Query $query, EQLQuery $condition = null) : array
    {
        return $this->_getByQueryEngine($query, $condition);
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
     * @param Entity $entity
     * @param bool $returnObject
     * @return Entity
     */
    public function create(Entity $entity, bool $returnObject = true)
    {
        return $this->_create($entity, $returnObject);
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
    public function getNewEntity() : Entity {
        return new Entity();
    }
}