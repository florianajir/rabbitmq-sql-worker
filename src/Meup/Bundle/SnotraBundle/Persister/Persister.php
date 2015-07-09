<?php
namespace Meup\Bundle\SnotraBundle\Persister;

use Exception;
use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\Factory\GenericEntityFactoryInterface;
use Meup\Bundle\SnotraBundle\Factory\RelationFactoryInterface;
use Meup\Bundle\SnotraBundle\Model\GenericEntityInterface;
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
     * @var GenericEntityFactoryInterface
     */
    protected $genericEntityFactory;

    /**
     * @var RelationFactoryInterface
     */
    protected $relationFactory;

    /**
     * @param ProviderInterface             $provider
     * @param GenericEntityFactoryInterface $genericEntityFactory
     * @param RelationFactoryInterface      $relationFactory
     */
    public function __construct(
        ProviderInterface $provider,
        GenericEntityFactoryInterface $genericEntityFactory,
        RelationFactoryInterface $relationFactory
    ) {
        $this->provider = $provider;
        $this->genericEntityFactory = $genericEntityFactory;
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
        foreach ($data as $table => $infos) {
            $entity = $this->genericEntityFactory->create($table, $infos);
            $this->persistRecursive($entity);
        }
    }

    /**
     * Persist data recursively
     *
     * @param GenericEntityInterface $entity
     *
     * @return array
     *
     * @throws Exception
     */
    protected function persistRecursive(GenericEntityInterface $entity)
    {
        //Persist oneToOne relations
        $entity->addDataSet($this->persistOneToOneRelations($entity));
        //persist manyToOne relations
        $entity->addDataSet($this->persistManyToOneRelations($entity));
        //insert the main subject
        $id = $this->insertOrUpdateIfExists($entity);
        //return id if no identifier
        if ($entity->getIdentifier() === null) {
            $entity->setIdentifier(array('id' => $id));
        }
        //persist oneToMany relations
        $this->persistOneToManyRelations($entity);
        //persist manyToMany relations (with joins)
        $this->persistManyToManyRelations($entity);

        return $entity->getIdentifier();
    }

    /**
     * Persist oneToOne relations
     *
     * @param GenericEntityInterface $entity
     *
     * @return array associative array( 'foreign_key' => 'value' )
     *
     * @throws Exception
     */
    protected function persistOneToOneRelations(GenericEntityInterface $entity)
    {
        $joinData = array();
        $relations = $entity->getOneToOneRelations();
        foreach ($relations as $relation) {
            $oneToOne = $this->relationFactory->create(DataMapper::RELATION_ONE_TO_ONE, $relation);
            $entity = $this->genericEntityFactory->create($oneToOne->getTable(), $oneToOne->getEntity());
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
     * @param GenericEntityInterface $entity
     *
     * @return array associative array( 'foreign_key' => 'value' )
     *
     * @throws Exception
     */
    protected function persistManyToOneRelations(GenericEntityInterface $entity)
    {
        $joinData = array();
        $relations = $entity->getManyToOneRelations();
        foreach ($relations as $relation) {
            $manyToOne = $this->relationFactory->create(DataMapper::RELATION_MANY_TO_ONE, $relation);
            $entity = $this->genericEntityFactory->create($manyToOne->getTable(), $manyToOne->getEntity());
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
     * @param GenericEntityInterface $entity
     *
     * @return int
     */
    protected function insertOrUpdateIfExists(GenericEntityInterface $entity)
    {
        return $this->provider->insertOrUpdateIfExists(
            $entity->getTable(),
            $entity->getData(),
            $entity->getIdentifier()
        );
    }

    /**
     * @param GenericEntityInterface $entity
     *
     * @throws Exception
     */
    protected function persistOneToManyRelations(GenericEntityInterface $entity)
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
            } else {
                throw new Exception("Unable to get an identifier. (Table: {$entity->getTable()})");
            }

            foreach ($oneToMany->getEntities() as $childData) {
                $child = $this->genericEntityFactory->create($oneToMany->getTable(), $childData[$oneToMany->getTable()]);
                $child->addDataSet(array($oneToMany->getJoinColumnName() => $foreignValue));
                $this->persistRecursive($child);
            }
        }
    }

    /**
     * @param GenericEntityInterface $entity
     *
     * @throws Exception
     */
    protected function persistManyToManyRelations(GenericEntityInterface $entity)
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
            } else {
                throw new Exception("Unable to get an identifier. (Table: {$entity->getTable()})");
            }
            $joinData = array(
                $manyToMany->getJoinColumnName() => $foreignValue
            );
            //delete joins before loop
            $this->provider->delete($manyToMany->getJoinTableName(), $joinData);
            foreach ($manyToMany->getEntities() as $childData) {
                $child = $this->genericEntityFactory->create($manyToMany->getTable(), $childData[$manyToMany->getTable()]);
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
