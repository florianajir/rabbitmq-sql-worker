<?php
namespace Meup\Bundle\SnotraBundle\Persister;

use Exception;
use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\Factory\EntityFactoryInterface;
use Meup\Bundle\SnotraBundle\Factory\RelationFactoryInterface;
use Meup\Bundle\SnotraBundle\Model\EntityInterface;
use Meup\Bundle\SnotraBundle\Provider\ProviderInterface;

/**
 * Class Persister
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class Persister implements PersisterInterface
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @var EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var RelationFactoryInterface
     */
    protected $relationFactory;

    /**
     * @param ProviderInterface        $provider
     * @param EntityFactoryInterface   $entityFactory
     * @param RelationFactoryInterface $relationFactory
     */
    public function __construct(
        ProviderInterface $provider,
        EntityFactoryInterface $entityFactory,
        RelationFactoryInterface $relationFactory
    ) {
        $this->provider = $provider;
        $this->entityFactory = $entityFactory;
        $this->relationFactory = $relationFactory;
    }

    /**
     * Persist data
     *
     * @param array $data An associative array containing column-value pairs.
     *
     * @throws Exception
     */
    public function persist(array $data)
    {
        foreach ($data as $infos) {
            $entity = $this->entityFactory->create($infos);
            $this->persistRecursive($entity);
        }
    }

    /**
     * Persist data recursively
     *
     * @param EntityInterface $entity
     *
     * @return array
     *
     * @throws Exception
     */
    protected function persistRecursive(EntityInterface $entity)
    {
        $entity->addDataSet($this->persistOneToOneRelations($entity));
        $entity->addDataSet($this->persistManyToOneRelations($entity));
        $id = $this->insertOrUpdateIfExists($entity);
        if ($entity->getIdentifier() === null) {
            $entity->setIdentifier(array('id' => $id));
        }
        $this->persistOneToManyRelations($entity);
        $this->persistManyToManyRelations($entity);

        return $entity->getIdentifier();
    }

    /**
     * Persist oneToOne relations
     *
     * @param EntityInterface $entity
     *
     * @return array associative array( 'foreign_key' => 'value' )
     *
     * @throws Exception
     */
    protected function persistOneToOneRelations(EntityInterface $entity)
    {
        $joinData = array();
        $relations = $entity->getOneToOneRelations();
        foreach ($relations as $relation) {
            $oneToOne = $this->relationFactory->create(DataMapper::RELATION_ONE_TO_ONE, $relation);
            $entity = $this->entityFactory->create($oneToOne->getEntity());
            $id = $this->persistRecursive($entity);
            $joinVal = $entity->getProperty($oneToOne->getJoinColumnReferencedColumnName());
            $joinData[$oneToOne->getJoinColumnName()] = !is_null($joinVal)
                ? $joinVal
                : $this->provider->getColumnValueWhere(
                    $oneToOne->getTable(),
                    $oneToOne->getJoinColumnReferencedColumnName(),
                    key($id),
                    current($id)
                );
        }

        return $joinData;
    }

    /**
     * Persist manyToOne relations
     *
     * @param EntityInterface $entity
     *
     * @return array associative array( 'foreign_key' => 'value' )
     *
     * @throws Exception
     */
    protected function persistManyToOneRelations(EntityInterface $entity)
    {
        $joinData = array();
        $relations = $entity->getManyToOneRelations();
        foreach ($relations as $relation) {
            $manyToOne = $this->relationFactory->create(DataMapper::RELATION_MANY_TO_ONE, $relation);
            $entity = $this->entityFactory->create($manyToOne->getEntity());
            $id = $this->persistRecursive($entity);
            $joinVal = $entity->getProperty($manyToOne->getJoinColumnReferencedColumnName());
            $joinData[$manyToOne->getJoinColumnName()] = !is_null($joinVal)
                ? $joinVal
                : $this->provider->getColumnValueWhere(
                    $manyToOne->getTable(),
                    $manyToOne->getJoinColumnReferencedColumnName(),
                    key($id),
                    current($id)
                );
        }

        return $joinData;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return int
     */
    protected function insertOrUpdateIfExists(EntityInterface $entity)
    {
        return $this->provider->insertOrUpdateIfExists(
            $entity->getTable(),
            $entity->getData(),
            $entity->getIdentifier()
        );
    }

    /**
     * @param EntityInterface $entity
     *
     * @throws Exception
     */
    protected function persistOneToManyRelations(EntityInterface $entity)
    {
        $relations = $entity->getOneToManyRelations();
        foreach ($relations as $relation) {
            $oneToMany = $this->relationFactory->create(DataMapper::RELATION_ONE_TO_MANY, $relation);
            $foreignValue = $entity->getProperty($oneToMany->getJoinColumnReferencedColumnName());
            $parentIdent = $entity->getIdentifier();
            if (is_null($foreignValue) && !empty($parentIdent)) {
                $foreignValue = $this->provider->getColumnValueWhere(
                    $entity->getTable(),
                    $oneToMany->getJoinColumnReferencedColumnName(),
                    key($parentIdent),
                    current($parentIdent)
                );
            }
            if (is_null($foreignValue)) {
                throw new Exception("Unable to get an identifier. (Table: {$entity->getTable()})");
            }

            foreach ($oneToMany->getEntities() as $childData) {
                $child = $this->entityFactory->create($childData[$oneToMany->getEntityName()]);
                $joinData = array($oneToMany->getJoinColumnName() => $foreignValue);
                foreach ($oneToMany->getReferences() as $field => $reference) {
                    $referenceValue = $this->provider->getColumnValueWhere(
                        $reference[DataMapper::MAPPING_KEY_TABLE],
                        $reference[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME],
                        key($reference[DataMapper::WHERE_KEY]),
                        current($reference[DataMapper::WHERE_KEY])
                    );
                    $joinData[$field] = $referenceValue;
                }
                $child->addDataSet($joinData);
                if ($oneToMany->isRemoveReferenced()) {
                    $this->provider->delete($oneToMany->getTable(), $joinData);
                }
                $this->persistRecursive($child);
            }
        }
    }

    /**
     * @param EntityInterface $entity
     *
     * @throws Exception
     */
    protected function persistManyToManyRelations(EntityInterface $entity)
    {
        $relations = $entity->getManyToManyRelations();
        foreach ($relations as $relation) {
            $manyToMany = $this->relationFactory->create(DataMapper::RELATION_MANY_TO_MANY, $relation);
            $foreignValue = $entity->getProperty($manyToMany->getJoinColumnReferencedColumnName());
            $parentIdent = $entity->getIdentifier();
            if (is_null($foreignValue) && !empty($parentIdent)) {
                $foreignValue = $this->provider->getColumnValueWhere(
                    $entity->getTable(),
                    $manyToMany->getJoinColumnReferencedColumnName(),
                    key($parentIdent),
                    current($parentIdent)
                );
            }
            if (is_null($foreignValue)) {
                throw new Exception("Unable to get an identifier. (Table: {$entity->getTable()})");
            }
            $joinData = array(
                $manyToMany->getJoinColumnName() => $foreignValue
            );
            //delete joins before loop
            $this->provider->delete($manyToMany->getJoinTableName(), $joinData);
            foreach ($manyToMany->getEntities() as $childData) {
                $child = $this->entityFactory->create($childData[$manyToMany->getEntityName()]);
                $this->persistRecursive($child);
                $newJoinDataValue = $child->getProperty($manyToMany->getInverseJoinColumnReferencedColumnName());
                $joinIdent = $child->getIdentifier();
                $joinData[$manyToMany->getInverseJoinColumnName()] = !is_null($newJoinDataValue)
                    ? $newJoinDataValue
                    : $this->provider->getColumnValueWhere(
                        $child->getTable(),
                        $manyToMany->getInverseJoinColumnReferencedColumnName(),
                        key($joinIdent),
                        current($joinIdent)
                    );
                //insert in join table
                $this->provider->insert($manyToMany->getJoinTableName(), $joinData);
            }
        }
    }
}
