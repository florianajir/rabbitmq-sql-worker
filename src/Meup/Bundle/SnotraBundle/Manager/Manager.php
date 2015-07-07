<?php
namespace Meup\Bundle\SnotraBundle\Manager;

use Exception;
use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformer;
use Meup\Bundle\SnotraBundle\Provider\ProviderInterface;

/**
 * Class Manager
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class Manager implements ManagerInterface
{
    /**
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
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
            $this->persistRecursive($table, $infos);
        }
    }

    /**
     * Persist data recursively
     *
     * @param string $table
     * @param array  $data        An associative array containing column-value pairs.
     * @param array  $foreignData to merge (for recursive calls)
     *
     * @return array
     *
     * @throws Exception
     */
    protected function persistRecursive($table, array $data, array $foreignData = array())
    {
        //get related index if defined
        $related = $this->popRelated($data);
        //Add foreign data
        $data = array_merge($data, $foreignData);
        //get subject identifier if defined
        $identifier = $this->popIdentifier($data);
        //Persist oneToOne relations
        if (!empty($related[DataMapper::RELATION_ONE_TO_ONE])) {
            $data = array_merge(
                $data,
                $this->persistToOneRelations($related[DataMapper::RELATION_ONE_TO_ONE])
            );
        }
        //persist manyToOne relations
        if (!empty($related[DataMapper::RELATION_MANY_TO_ONE])) {
            $data = array_merge(
                $data,
                $this->persistToOneRelations($related[DataMapper::RELATION_MANY_TO_ONE])
            );
        }
        //insert the main subject
        $id = $this->provider->insertOrUpdateIfExists(
            $table,
            $data,
            $identifier
        );
        //persist oneToMany relations
        if (!empty($related[DataMapper::RELATION_ONE_TO_MANY])) {
            $this->persistOneToManyRelations($related[DataMapper::RELATION_ONE_TO_MANY], $table, $identifier);
        }
        //persist manyToMany relations (with joins)
        if (!empty($related[DataMapper::RELATION_MANY_TO_MANY])) {
            $this->persistManyToManyRelations($related[DataMapper::RELATION_MANY_TO_MANY], $table, $identifier);
        }
        //prepare return
        if (empty($identifier)) {
            $identifier = array('id' => $id);
        }

        return $identifier;
    }

    /**
     * Get the related index if defined and remove it from data to persist
     *
     * @param array $data
     *
     * @return array|null
     */
    protected function popRelated(array &$data)
    {
        $related = null;
        if (array_key_exists(DataTransformer::RELATED_KEY, $data)) {
            $related = $data[DataTransformer::RELATED_KEY];
            unset($data[DataTransformer::RELATED_KEY]);
        }

        return $related;
    }

    /**
     * Get the subject identifier if defined and remove it from data to persist
     *
     * @param array $infos
     *
     * @return array|null
     */
    protected function popIdentifier(array &$infos)
    {
        //get subject identifier if defined
        $identifier = null;
        if (array_key_exists(DataTransformer::IDENTIFIER_KEY, $infos)) {
            $identifierName = $infos[DataTransformer::IDENTIFIER_KEY];
            unset($infos[DataTransformer::IDENTIFIER_KEY]);
            if (!is_null($identifierName) && isset($infos[$identifierName])) {
                $identifier = array($identifierName => $infos[$identifierName]);
            }
        }

        return $identifier;
    }

    /**
     * Persist oneToOne & manyToOne relations
     *
     * @param array $relations
     *
     * @return array associative array( 'foreign_key' => 'value' )
     *
     * @throws Exception
     */
    protected function persistToOneRelations(array $relations)
    {
        $joinData = array();
        foreach ($relations as $toOne) {
            $relation = $toOne[DataTransformer::RELATED_RELATION_KEY];
            $tableName = $relation[DataMapper::MAPPING_KEY_TABLE];
            $data = $toOne[DataTransformer::RELATED_DATA_KEY][$tableName];
            $joinColumn = $relation[DataMapper::RELATION_KEY_JOIN_COLUMN];
            $referencedColumnName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
            $foreignKey = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
            $id = $this->persistRecursive(
                $tableName,
                $data
            );
            $joinData[$foreignKey] = isset($data[$referencedColumnName])
                ? $data[$referencedColumnName]
                : $this->provider->getColumnValueWhere(
                    $tableName,
                    $referencedColumnName,
                    key($id),
                    current($id)
                );
        }

        return $joinData;
    }

    /**
     * @param array  $relations
     * @param string $relatedTable
     * @param array  $identifier
     *
     * @throws Exception
     */
    protected function persistOneToManyRelations(array $relations, $relatedTable, array $identifier)
    {
        foreach ($relations as $oneToMany) {
            $relation = $oneToMany[DataTransformer::RELATED_RELATION_KEY];
            $joinColumn = $relation[DataMapper::RELATION_KEY_JOIN_COLUMN];
            $referencedColumn = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
            //set foreign_key on relation data
            if (isset($infos[$referencedColumn])) {
                $foreignKeyValue = $infos[$referencedColumn];
            } elseif (isset($identifier)) {
                $foreignKeyValue = $this->provider->getColumnValueWhere(
                    $relatedTable,
                    $referencedColumn,
                    key($identifier),
                    current($identifier)
                );
            } else {
                throw new Exception("Unable to get an identifier. (Table: $relatedTable)");
            }
            foreach ($oneToMany[DataTransformer::RELATED_DATA_KEY] as $element) {
                $this->persistRecursive(
                    $relation[DataMapper::MAPPING_KEY_TABLE],
                    $element[$relation[DataMapper::MAPPING_KEY_TABLE]],
                    array(
                        $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME] => $foreignKeyValue
                    )
                );
            }
        }
    }

    /**
     * @param array  $relations
     * @param string $relatedTable
     * @param array  $identifier
     *
     * @throws Exception
     */
    protected function persistManyToManyRelations(array $relations, $relatedTable, array $identifier)
    {
        foreach ($relations as $manyToMany) {
            $relation = $manyToMany[DataTransformer::RELATED_RELATION_KEY];
            $joinTable = $relation[DataMapper::RELATION_KEY_JOIN_TABLE];
            $joinTableName = $joinTable[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
            $joinColumn = $joinTable[DataMapper::RELATION_KEY_JOIN_COLUMN];
            $joinColumnReferenced = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
            $inverseJoinColumn = $joinTable[DataMapper::RELATION_KEY_INVERSE_JOIN_COLUMN];
            $inverseJoinColumnName = $inverseJoinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
            $inverseJoinColumnReferenced = $inverseJoinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
            if (isset($infos[$joinColumnReferenced])) {
                $joinValue = $infos[$joinColumnReferenced];
            } elseif (isset($identifier)) {
                $joinValue = $this->provider->getColumnValueWhere(
                    $relatedTable,
                    $joinColumnReferenced,
                    key($identifier),
                    current($identifier)
                );
            } else {
                throw new Exception("Unable to get an identifier. (Table: $relatedTable)");
            }
            $joinData = array(
                $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME] => $joinValue
            );
            $this->provider->delete($joinTableName, $joinData);

            foreach ($manyToMany[DataTransformer::RELATED_DATA_KEY] as $element) {
                $id = $this->persistRecursive(
                    $relation[DataMapper::MAPPING_KEY_TABLE],
                    $element[$relation[DataMapper::MAPPING_KEY_TABLE]]
                );
                $joinData[$inverseJoinColumnName] = isset($element[$inverseJoinColumnReferenced])
                    ? $element[$inverseJoinColumnReferenced]
                    : $this->provider->getColumnValueWhere(
                        $relation[DataMapper::MAPPING_KEY_TABLE],
                        $inverseJoinColumnReferenced,
                        key($id),
                        current($id)
                    );
                //insert in join table
                $this->provider->insert($joinTableName, $joinData);
            }
        }
    }
}
