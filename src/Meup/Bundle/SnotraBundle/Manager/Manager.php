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
     * Insert or update a record if exists
     *
     * @param array $data An associative array containing column-value pairs.
     *
     * @throws Exception
     */
    public function persist(array $data)
    {
        foreach ($data as $table => $infos) {
            $related = $infos[DataTransformer::RELATED_KEY];
            unset($infos[DataTransformer::RELATED_KEY]);
            //persist oneToOne relations
            $oneToOne = $related[DataMapper::RELATION_ONE_TO_ONE];
            foreach ($oneToOne as $dependency) {
                array_merge(
                    $infos,
                    $this->persistToOneRelated($dependency, true)
                );
            }
            //persist manyToOne relations
            $manyToOne = $related[DataMapper::RELATION_MANY_TO_ONE];
            foreach ($manyToOne as $dependency) {
                array_merge(
                    $infos,
                    $this->persistToOneRelated($dependency, true)
                );
            }
            //get subject identifier if defined
            $identifier = null;
            if (isset($infos[DataTransformer::IDENTIFIER_KEY])) {
                $identifierName = $infos[DataTransformer::IDENTIFIER_KEY];
                unset($infos[DataTransformer::IDENTIFIER_KEY]);
                if (isset($infos[$identifierName])) {
                    $identifier = array($identifierName => $infos[$identifierName]);
                }
            }
            //insert the main subject
            $id = $this->provider->insertOrUpdateIfExists(
                $table,
                $infos,
                $identifier
            );
            //persist oneToMany relations
            $oneToMany = $related[DataMapper::RELATION_ONE_TO_MANY];
            if (!empty($oneToMany)) {
                foreach ($oneToMany as $linked) {
                    $relation = $linked[DataTransformer::RELATED_INFOS_KEY];
                    $relatedTableName = $relation[DataMapper::MAPPING_KEY_TABLE];
                    $joinColumn = $relation[DataMapper::RELATION_KEY_JOIN_COLUMN];
                    $foreignKeyName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
                    $referencedColumn = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
                    //set foreign_key on relation data
                    $foreignKeyValue = null;
                    if (isset($infos[$referencedColumn])) {
                        $foreignKeyValue = $infos[$referencedColumn];
                    } elseif (isset($identifier)) {
                        $foreignKeyValue = $this->provider->getColumnValueWhere($table, $referencedColumn,
                            key($identifier), current($identifier));
                    } elseif ($id) {
                        $foreignKeyValue = $this->provider->getColumnValueWhere($table, $referencedColumn, 'id', $id);
                    } else {
                        throw new Exception("Unable to get last insert id. Set an identifier to your mapping. (Table: $table)");
                    }
                    $linked[DataTransformer::RELATED_DATA_KEY][$relatedTableName][$foreignKeyName] = $foreignKeyValue;
                    $this->persistOneToManyRelated($linked);
                }
            }
            //persist manyToMany relations (with joins)
            $manyToMany = $related[DataMapper::RELATION_MANY_TO_MANY];
            foreach ($manyToMany as $linked) {
                $joinData = array();
                $relation = $linked[DataTransformer::RELATED_INFOS_KEY];
                $joinTable = $relation[DataMapper::RELATION_KEY_JOIN_TABLE];
                $joinColumn = $joinTable[DataMapper::RELATION_KEY_JOIN_COLUMN];
                $joinColumnName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
                $joinColumnReferenced = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
                if (isset($infos[$joinColumnReferenced])) {
                    $joinData[$joinColumnName] = $infos[$joinColumnReferenced];
                } elseif (isset($identifier)) {
                    $joinData[$joinColumnName] = $this->provider->getColumnValueWhere(
                        $table,
                        $joinColumnReferenced,
                        key($identifier),
                        current($identifier)
                    );
                } elseif ($joinColumnReferenced === 'id') {
                    $joinData[$joinColumnName] = $id;
                } else {
                    $joinData[$joinColumnName] = $this->provider->getColumnValueWhere(
                        $table,
                        $joinColumnReferenced,
                        'id',
                        $id
                    );
                }
                $this->persistManyToManyRelated($linked, $joinData);
            }
        }
    }

    /**
     * Persist oneToOne & manyToOne relations
     *
     * @param array $related
     *
     * @return array associative array( 'foreign_key' => 'value' )
     *
     * @throws Exception
     */
    protected function persistToOneRelated(array $related)
    {
        $relation = $related[DataTransformer::RELATED_INFOS_KEY];
        $tableName = $relation[DataMapper::MAPPING_KEY_TABLE];
        $data = $related[DataTransformer::RELATED_DATA_KEY][$tableName];
        $joinColumn = $relation[DataMapper::RELATION_KEY_JOIN_COLUMN];
        $referencedColumnName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
        $foreign_key = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $foreign_val = isset($data[$referencedColumnName])
            ? $data[$referencedColumnName]
            : null;
        if (array_key_exists(DataTransformer::IDENTIFIER_KEY, $data)) {
            $identifierName = $data[DataTransformer::IDENTIFIER_KEY];
            unset($data[DataTransformer::IDENTIFIER_KEY]);
            if (isset($data[$identifierName])) {
                $identifierValue = $data[$identifierName];
                $foreign_id = $this->provider->insertOrUpdateIfExists(
                    $tableName,
                    $data,
                    array(
                        $identifierName => $identifierValue
                    )
                );
                if (is_null($foreign_val)) {
                    $foreign_val = $this->provider->getColumnValueWhere(
                        $tableName,
                        $referencedColumnName,
                        $identifierName,
                        $identifierValue
                    );
                }
            } else {
                $foreign_id = $this->provider->insert(
                    $tableName,
                    $data
                );
            }
        } else {
            $foreign_id = $this->provider->insert(
                $tableName,
                $data
            );
        }
        if (is_null($foreign_val)) {
            if (!$foreign_id) {
                throw new Exception("Unable to get last insert id. Set an identifier to your mapping. (Table: $tableName)");
            }
            $foreign_val = ($referencedColumnName == 'id')
                ? $foreign_id
                : $this->provider->getColumnValueWhere($tableName, $referencedColumnName, 'id', $foreign_id);
            if (is_null($foreign_val)) {
                throw new Exception("Unable to get foreign_key value. (Table: $tableName)");
            }
        }

        return array(
            $foreign_key => $foreign_val
        );
    }


    /**
     * Persist oneToMany relation
     *
     * @param array $related
     *
     * @return int inserted row id
     *
     * @throws Exception
     */
    protected function persistOneToManyRelated(array $related)
    {
        $relation = $related[DataTransformer::RELATED_INFOS_KEY];
        $tableName = $relation[DataMapper::MAPPING_KEY_TABLE];
        $data = $related[DataTransformer::RELATED_DATA_KEY][$tableName];
        if (array_key_exists(DataTransformer::IDENTIFIER_KEY, $data)) {
            $identifierName = $data[DataTransformer::IDENTIFIER_KEY];
            unset($data[DataTransformer::IDENTIFIER_KEY]);
            if (isset($data[$identifierName])) {
                $identifierValue = $data[$identifierName];
                $foreign_id = $this->provider->insertOrUpdateIfExists(
                    $tableName,
                    $data,
                    array(
                        $identifierName => $identifierValue
                    )
                );
            } else {
                $foreign_id = $this->provider->insert(
                    $tableName,
                    $data
                );
            }
        } else {
            $foreign_id = $this->provider->insert(
                $tableName,
                $data
            );
        }

        return $foreign_id;
    }

    /**
     * @param array $related
     * @param array $joinData
     *
     * @return array|bool
     * @throws Exception
     */
    protected function persistManyToManyRelated(array $related, array $joinData)
    {
        $relation = $related[DataTransformer::RELATED_INFOS_KEY];
        $tableName = $relation[DataMapper::MAPPING_KEY_TABLE];
        $joinTable = $relation[DataMapper::RELATION_KEY_JOIN_TABLE];
        $joinTableName = $joinTable[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $inverseJoinColumn = $joinTable[DataMapper::RELATION_KEY_INVERSE_JOIN_COLUMN];
        $inverseJoinColumnName = $inverseJoinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $inverseJoinColumnReferenced = $inverseJoinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];

        $data = $related[DataTransformer::RELATED_DATA_KEY][$tableName];
        $foreign_val = isset($data[$inverseJoinColumnReferenced])
            ? $data[$inverseJoinColumnReferenced]
            : null;

        if (array_key_exists(DataTransformer::IDENTIFIER_KEY, $data)) {
            $identifierName = $data[DataTransformer::IDENTIFIER_KEY];
            unset($data[DataTransformer::IDENTIFIER_KEY]);
            if (isset($data[$identifierName])) {
                $identifierValue = $data[$identifierName];
                $foreign_id = $this->provider->insertOrUpdateIfExists(
                    $tableName,
                    $data,
                    array(
                        $identifierName => $identifierValue
                    )
                );
                if (is_null($foreign_val)) {
                    $foreign_val = $this->provider->getColumnValueWhere(
                        $tableName,
                        $inverseJoinColumnReferenced,
                        $identifierName,
                        $identifierValue
                    );
                }
            } else {
                $foreign_id = $this->provider->insert(
                    $tableName,
                    $data
                );
            }
        } else {
            $foreign_id = $this->provider->insert(
                $tableName,
                $data
            );
        }
        if (is_null($foreign_val)) {
            if (!$foreign_id) {
                throw new Exception("Unable to get last insert id. Set an identifier to your mapping. (Table: $tableName)");
            }
            $foreign_val = ($inverseJoinColumnReferenced === 'id')
                ? $foreign_id
                : $this->provider->getColumnValueWhere(
                    $tableName,
                    $inverseJoinColumnReferenced,
                    'id',
                    $foreign_id
                );
            if (is_null($foreign_val)) {
                throw new Exception("Unable to get foreign_key value. (Table: $tableName)");
            }
        }
        $joinData[$inverseJoinColumnName] = $foreign_val;
        $this->provider->insert($joinTableName, $joinData);

        return $foreign_val;
    }
}
