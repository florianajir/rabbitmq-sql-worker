<?php
namespace Ajir\RabbitMqSqlBundle\DataMapper;

use InvalidArgumentException;

/**
 * Class DataMapper
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
class DataMapper implements DataMapperInterface
{
    const MAPPING_KEY_TABLE = 'table';
    const MAPPING_KEY_IDENTIFIER = 'identifier';
    const MAPPING_KEY_LENGTH = 'length';
    const MAPPING_KEY_COLUMN = 'column';
    const MAPPING_KEY_FIELDS = 'fields';
    const MAPPING_KEY_TYPE = 'type';
    const MAPPING_KEY_NULLABLE = 'nullable';
    const MAPPING_KEY_DISCRIMINATOR = 'discriminator';

    const RELATION_ONE_TO_ONE = 'oneToOne';
    const RELATION_ONE_TO_MANY = 'oneToMany';
    const RELATION_MANY_TO_ONE = 'manyToOne';
    const RELATION_MANY_TO_MANY = 'manyToMany';

    const RELATION_KEY_TARGET_ENTITY = 'targetEntity';
    const RELATION_KEY_JOIN_COLUMN = 'joinColumn';
    const RELATION_KEY_JOIN_COLUMN_NAME = 'name';
    const RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME = 'referencedColumnName';
    const RELATION_KEY_JOIN_TABLE = 'joinTable';
    const RELATION_KEY_INVERSE_JOIN_COLUMN = 'inverseJoinColumn';

    const REFERENCES_KEY = 'references';
    const WHERE_KEY = 'where';
    const REMOVE_REFERENCED_KEY = 'removeReferenced';
    const FIXED_VALUE = 'value';

    /**
     * @var array
     */
    protected $mapping;

    /**
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param string $entity
     *
     * @return array
     */
    public function getFieldsName($entity)
    {
        $fieldsName = array();
        if (isset($this->mapping[$entity][self::MAPPING_KEY_FIELDS])) {
            $fieldsName = array_keys($this->mapping[$entity][self::MAPPING_KEY_FIELDS]);
        }

        return $fieldsName;
    }

    /**
     * @param string $entity
     * @param string $field
     *
     * @return int|null
     */
    public function getFieldMaxLength($entity, $field)
    {
        $maxLength = null;
        $mapping = $this->getFieldMapping($entity, $field);
        if (isset($mapping[self::MAPPING_KEY_LENGTH])) {
            if (!is_numeric($mapping[self::MAPPING_KEY_LENGTH])) {
                throw new InvalidArgumentException(
                    "$entity.$field " .
                    self::MAPPING_KEY_LENGTH .
                    " mapping property must be a numeric value."
                );
            }
            $maxLength = intval($mapping[self::MAPPING_KEY_LENGTH]);
        }

        return $maxLength;
    }

    /**
     * @param string $entity
     * @param string $field
     *
     * @return array
     */
    public function getFieldMapping($entity, $field)
    {
        $mapping = array();
        if (isset($this->mapping[$entity][self::MAPPING_KEY_FIELDS][$field])) {
            $mapping = $this->mapping[$entity][self::MAPPING_KEY_FIELDS][$field];
        }

        return $mapping;
    }

    /**
     * @param string $entity
     * @param string $field
     *
     * @return string|null
     */
    public function getFieldType($entity, $field)
    {
        $type = null;
        $mapping = $this->getFieldMapping($entity, $field);
        if (isset($mapping[self::MAPPING_KEY_TYPE])) {
            $type = $mapping[self::MAPPING_KEY_TYPE];
        }

        return $type;
    }

    /**
     * Is the field nullable from mapping (default:true)
     *
     * @param string $entity
     * @param string $field
     *
     * @return bool
     */
    public function isFieldNullable($entity, $field)
    {
        $nullable = true;
        $mapping = $this->getFieldMapping($entity, $field);
        if (isset($mapping[self::MAPPING_KEY_NULLABLE])) {
            $nullable =
                $mapping[self::MAPPING_KEY_NULLABLE] !== false
                && $mapping[self::MAPPING_KEY_NULLABLE] !== 'false';
        }

        return $nullable;
    }

    /**
     * @param string $container
     * @param string $entity
     *
     * @return string|null
     */
    public function getRelation($container, $entity)
    {
        $relations = array(
            self::RELATION_MANY_TO_MANY,
            self::RELATION_MANY_TO_ONE,
            self::RELATION_ONE_TO_ONE,
            self::RELATION_ONE_TO_MANY,
        );

        foreach ($relations as $relation) {
            if (isset($this->mapping[$container][$relation][$entity])) {
                return $relation;
            }
        }

        return null;
    }

    /**
     * @param string $entity
     *
     * @return string|null
     */
    public function getIdentifier($entity)
    {
        $identifier = null;
        if (isset($this->mapping[$entity][self::MAPPING_KEY_IDENTIFIER])) {
            $identifier = $this->mapping[$entity][self::MAPPING_KEY_IDENTIFIER];
        }

        return $identifier;
    }

    /**
     * @param string $container
     * @param string $entity
     * @param string $relation
     *
     * @return string|null
     */
    public function getTargetEntity($container, $entity, $relation)
    {
        $targetEntity = null;
        $infos = $this->getRelationInfos($container, $entity, $relation);
        if (isset($infos[self::RELATION_KEY_TARGET_ENTITY])) {
            $targetEntity = $infos[self::RELATION_KEY_TARGET_ENTITY];
        }

        return $targetEntity;
    }

    /**
     * @param string $container
     * @param string $entity
     * @param string $relation
     *
     * @return array|null
     */
    public function getRelationInfos($container, $entity, $relation)
    {
        $details = null;
        if (isset($this->mapping[$container][$relation][$entity])) {
            $details = $this->mapping[$container][$relation][$entity];
            $details[self::MAPPING_KEY_TABLE] = $this->getTableName($details[self::RELATION_KEY_TARGET_ENTITY]);
        }

        return $details;
    }

    /**
     * @param string $entity
     *
     * @return string|null
     */
    public function getTableName($entity)
    {
        $tableName = null;
        if (isset($this->mapping[$entity][self::MAPPING_KEY_TABLE])) {
            $tableName = $this->mapping[$entity][self::MAPPING_KEY_TABLE];
        }

        return $tableName;
    }

    /**
     * @param string $entity
     *
     * @return string|null
     */
    public function getDiscriminator($entity)
    {
        $discr = null;
        if (isset($this->mapping[$entity][self::MAPPING_KEY_DISCRIMINATOR])) {
            $discr = $this->mapping[$entity][self::MAPPING_KEY_DISCRIMINATOR];
        }

        return $discr;
    }

    /**
     * @param string $container
     * @param string $entity
     * @param string $relation
     *
     * @return array|null
     */
    public function getJoinTable($container, $entity, $relation)
    {
        $joinTable = null;
        $infos = $this->getRelationInfos($container, $entity, $relation);
        if (isset($infos[self::RELATION_KEY_JOIN_TABLE])) {
            $joinTable = $infos[self::RELATION_KEY_JOIN_TABLE];
        }

        return $joinTable;
    }

    /**
     * @param string $relation
     *
     * @return bool
     */
    public function isCollection($relation)
    {
        $collections = array(self::RELATION_ONE_TO_MANY, self::RELATION_MANY_TO_MANY);

        return in_array($relation, $collections);
    }

    /**
     * Get the mapping column => fixed_value for a field
     *
     * @param string $entity
     * @param string $field
     *
     * @return array
     */
    public function getFixedFieldMapping($entity, $field)
    {
        $mapping = array();
        $fixed = $this->getFixedValue($entity, $field);
        if (isset($fixed)) {
            $mapping = array(
                $this->getFieldColumn($entity, $field) => $fixed
            );
        }

        return $mapping;
    }

    /**
     * Get the fixed field value
     *
     * @param string $entity
     * @param string $field
     *
     * @return string
     */
    public function getFixedValue($entity, $field)
    {
        $fixed = null;
        if (isset($this->mapping[$entity][self::MAPPING_KEY_FIELDS][$field][self::FIXED_VALUE])) {
            $fixed = $this->mapping[$entity][self::MAPPING_KEY_FIELDS][$field][self::FIXED_VALUE];
        }

        return $fixed;
    }

    /**
     * @param string $entity
     * @param string $field
     *
     * @return string|null
     */
    public function getFieldColumn($entity, $field)
    {
        $column = $field;
        $mapping = $this->getFieldMapping($entity, $field);
        if (isset($mapping[self::MAPPING_KEY_COLUMN])) {
            $column = $mapping[self::MAPPING_KEY_COLUMN];
        }

        return $column;
    }
}
