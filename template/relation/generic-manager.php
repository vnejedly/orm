<?= '<?php' ?>
<?php /**
 * @var DateTime $generatedDateTime
 * @var string $className
 * @var string $relationEntityNamespace
 * @var string $relationManagerGenericNamespace
 * @var string $relationManagerNamespace
 * @var string $persistenceManagerNamespace
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\FieldInfo[] $fields
 * @var \Hooloovoo\ORM\Generator\Relation\Definer\Component\ComponentTable $parentComponent
 */ ?>
namespace <?= $relationManagerGenericNamespace ?>;

<?php foreach ($fields as $field): ?>
<?php if ($field->isPersistence()): ?>
use <?= $persistenceManagerNamespace ?>\<?= $field->getFieldEntityName() ?> as <?= $field->getFieldEntityManagerAlias() ?>;
<?php else: ?>
use <?= $relationManagerGenericNamespace ?>\<?= $field->getFieldEntityName() ?>  as <?= $field->getFieldEntityManagerAlias() ?>;
<?php endif; ?>
<?php endforeach; ?>
use <?= $persistenceManagerNamespace ?>\<?= $parentComponent->getComponentEntityName() ?> as <?= $parentComponent->getComponentEntityName() ?>PM;
use <?= $relationEntityNamespace ?>\<?= $className ?> as Entity;
use Hooloovoo\ORM\Relation\EQLQuery\EQLQueryInterface;
use Hooloovoo\ORM\Relation\Manager\AbstractManager;
use Hooloovoo\ORM\Relation\GroupedArray;
use Hooloovoo\Database\Database;
use Hooloovoo\QueryEngine\Query\Query;

/**
 * Class <?= $className ?>
 *
 * +-------------------------------------------------------+
 * ! this class is auto-generated, please do not change it !
 * +-------------------------------------------------------+
 *
 * <?= $generatedDateTime->format('c') ?>
 */
class <?= $className ?> extends AbstractManager
{
    /**
     * <?= $className ?> constructor.
     *
     * @param Database $database
     * @param <?= $parentComponent->getComponentEntityName() ?>PM $<?= $parentComponent->getComponentFieldName() ?>PM,
     <?php foreach ($fields as $field): ?>
     * @param <?= $field->getFieldEntityManagerAlias() ?> $<?= $field->getFieldEntityManagerVariableName() ?>
     <?php endforeach; ?>
     */
    public function __construct(
        Database $database,
        <?= $parentComponent->getComponentEntityName() ?>PM $<?= $parentComponent->getComponentFieldName() ?>PM,
        <?php foreach ($fields as $field): ?>
        <?= $field->getFieldEntityManagerAlias() ?> $<?= $field->getFieldEntityManagerVariableName() ?><?= $this->_delimit($fields, ',') ?>
        <?php endforeach; ?>
    ) {
        $this->setDatabase($database);
        $this->addPersistenceManager($<?= $parentComponent->getComponentFieldName() ?>PM, true);
        <?php foreach ($fields as $field): ?>
        <?php if ($field->isPersistence()): ?>
        $this->addPersistenceManager($<?= $field->getFieldEntityManagerVariableName() ?>);
        <?php else: ?>
        $this->addRelationManager($<?= $field->getFieldEntityManagerVariableName() ?>);
        <?php endif; ?>
        <?php endforeach; ?>
    }

    /**
     * @return EQLQueryInterface
     */
    protected function getBasicCompositeSelect(): EQLQueryInterface
    {
        return $this->getEQLQuery("
            SELECT {*.$} FROM {<?= $parentComponent->getComponentTableMapping()->getEntityName() ?>}
            <?php foreach ($fields as $field): ?>
            <?php foreach ($field->getJoinClauses() as $joinClause): ?>
            <?= $joinClause ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
        ");
    }

    /**
     * @param GroupedArray $groupedArray
     * @return Entity
     */
    protected function getEntityFromGroupedArray(GroupedArray $groupedArray)
    {
        return new Entity(
            $groupedArray->getSingleComponent('<?= $parentComponent->getComponentTableMapping()->getName() ?>'),
            <?php foreach ($fields as $field): ?>
            $groupedArray->get<?= $field->isCollection() ? 'Collection' : 'SingleComponent'; ?>('<?= $field->getFieldTableName() ?>')<?= $this->_delimit($fields, ',') ?>
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
     * @param EQLQueryInterface $condition
     * @return Entity
     */
    public function getSingleByCondition(EQLQueryInterface $condition) : Entity
    {
        return $this->_getSingleByCondition($condition);
    }

    /**
     * @param EQLQueryInterface $condition
     * @return Entity[]
     */
    public function getByCondition(EQLQueryInterface $condition) : array
    {
        return $this->_getByCondition($condition);
    }

    /**
     * @param Query $query
     * @param EQLQueryInterface|null $condition
     * @return Entity[]
     */
    public function getByQueryEngine(Query $query, EQLQueryInterface $condition = null) : array
    {
        return $this->_getByQueryEngine($query, $condition);
    }

    /**
     * @return <?= $parentComponent->getComponentEntityName() ?>PM
     */
    public function get<?= $parentComponent->getComponentEntityName() ?>PersistenceManager() : <?= $parentComponent->getComponentEntityName() ?>PM
    {
        return $this->_componentManagers['<?= $parentComponent->getComponentTableMapping()->getName() ?>'];
    }
    <?php foreach ($fields as $field): ?>
    <?php if ($field->isPersistence()): ?>

    /**
     * @return <?= $field->getFieldEntityName() ?>PM
     */
    public function get<?= $field->getFieldEntityName() ?>PersistenceManager() : <?= $field->getFieldEntityName() ?>PM
    {
        return $this->_componentManagers['<?= $field->getFieldTableName() ?>'];
    }
    <?php else: ?>

    /**
     * @return <?= $field->getFieldEntityName() ?>RM
     */
    public function get<?= $field->getFieldEntityName() ?>RelationManager() : <?= $field->getFieldEntityName() ?>RM
    {
        return $this->_componentManagers['<?= $field->getFieldTableName() ?>'];
    }
    <?php endif; ?>
    <?php endforeach; ?>
}
