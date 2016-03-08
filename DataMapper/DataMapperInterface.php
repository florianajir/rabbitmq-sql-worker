<?php
namespace Ajir\RabbitMqSqlBundle\DataMapper;

/**
 * Interface DataMapperInterface
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
interface DataMapperInterface
{
    /**
     * @param string $entity
     *
     * @return array
     */
    public function getFieldsName($entity);

    /**
     * @param string $entity
     * @param string $field
     *
     * @return string
     */
    public function getFieldColumn($entity, $field);

    /**
     * @param string $entity
     * @param string $field
     *
     * @return array
     */
    public function getFieldMapping($entity, $field);

    /**
     * @param string $entity
     * @param string $field
     *
     * @return int
     */
    public function getFieldMaxLength($entity, $field);

    /**
     * @param string $entity
     * @param string $field
     *
     * @return bool
     */
    public function isFieldNullable($entity, $field);

    /**
     * @param string $entity
     * @param string $field
     *
     * @return string
     */
    public function getFieldType($entity, $field);

    /**
     * @param string $entity
     *
     * @return string|null
     */
    public function getIdentifier($entity);

    /**
     * @param string $container
     * @param string $entity
     * @param string $relation
     *
     * @return array
     */
    public function getJoinTable($container, $entity, $relation);

    /**
     * @param string $container
     * @param string $entity
     *
     * @return string|null
     */
    public function getRelation($container, $entity);

    /**
     * @param string $container
     * @param string $entity
     * @param string $relation
     *
     * @return array
     */
    public function getRelationInfos($container, $entity, $relation);

    /**
     * @param string $entity
     *
     * @return string
     */
    public function getTableName($entity);

    /**
     * @param string $entity
     *
     * @return string|null
     */
    public function getDiscriminator($entity);

    /**
     * @param string $container
     * @param string $entity
     * @param string $relation
     *
     * @return string
     */
    public function getTargetEntity($container, $entity, $relation);

    /**
     * @param string $relation
     *
     * @return bool
     */
    public function isCollection($relation);

    /**
     * Get a column => fixed_value mapping for a field
     *
     * @param string $entity
     * @param string $field
     *
     * @return array
     */
    public function getFixedFieldMapping($entity, $field);

    /**
     * Get the fixed field value
     *
     * @param string $entity
     * @param string $field
     *
     * @return string
     */
    public function getFixedValue($entity, $field);
}
