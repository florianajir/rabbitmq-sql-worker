<?php
namespace Ajir\RabbitMqSqlBundle\DataTransformer;

use InvalidArgumentException;
use Ajir\RabbitMqSqlBundle\DataMapper\DataMapperInterface;
use Ajir\RabbitMqSqlBundle\DataValidator\DataValidatorInterface;

/**
 * Class DataTransformer
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
class DataTransformer implements DataTransformerInterface
{
    const RELATED_KEY = '_related';
    const RELATED_RELATION_KEY = '_relation';
    const RELATED_DATA_KEY = '_data';
    const IDENTIFIER_KEY = '_identifier';
    const DISCRIMINATOR_KEY = '_discriminator';
    const TABLE_KEY = '_table';

    /**
     * @var DataMapperInterface
     */
    protected $mapper;

    /**
     * @var DataValidatorInterface
     */
    protected $validator;

    /**
     * @param DataMapperInterface    $mapper
     * @param DataValidatorInterface $validator
     */
    public function __construct(DataMapperInterface $mapper, DataValidatorInterface $validator = null)
    {
        $this->mapper = $mapper;
        $this->validator = $validator;
    }

    /**
     * Prepare data from mapping rules (recursive)
     *
     * @param string $type
     * @param array  $data
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function prepare($type, array $data)
    {
        $prepared = array();
        $data = $this->prepareData($type, $data);
        $prepared[$type] = $this->checkFieldsMapping($type, $data);

        return $prepared;
    }

    /**
     * @param string $type
     * @param array  $data
     *
     * @return array
     */
    protected function prepareData($type, array $data)
    {
        $prepared = array();
        if ($identifier = $this->mapper->getIdentifier($type)) {
            $prepared[self::IDENTIFIER_KEY] = $identifier;
        }
        if ($tableName = $this->mapper->getTableName($type)) {
            $prepared[self::TABLE_KEY] = $tableName;
        }
        if ($discr = $this->mapper->getDiscriminator($type)) {
            if (array_key_exists($discr, $data)) {
                $prepared[self::TABLE_KEY] = $data[$discr];
            }
        }
        foreach ($data as $field => $value) {
            $fieldMapping = $this->mapper->getFieldMapping($type, $field);
            if (!empty($fieldMapping)) {
                $this->prepareField($prepared, $type, $field, $value);
            } elseif ($relation = $this->mapper->getRelation($type, $field)) {
                $this->prepareRelated($prepared, $type, $field, $value, $relation);
            }
        }

        return $prepared;
    }

    /**
     * @param array  &$prepared
     * @param string $type
     * @param string $field
     * @param string $value
     *
     * @return array
     */
    protected function prepareField(array &$prepared, $type, $field, $value)
    {
        if ($this->validator) {
            $this->validate($type, $field, $value);
        }
        $fieldColumn = $this->mapper->getFieldColumn($type, $field);
        $prepared[$fieldColumn] = $value;

        return $prepared;
    }

    /**
     * @param string $type
     * @param string $field
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     */
    protected function validate($type, $field, $value)
    {
        // validate length if defined
        if ($maxLength = $this->mapper->getFieldMaxLength($type, $field)) {
            if (!$this->validator->validateLength($value, $maxLength)) {
                throw new InvalidArgumentException($type . '.' . $field . ' value length exceed database max length.');
            }
        }
        // validate field type
        if ($fieldType = $this->mapper->getFieldType($type, $field)) {
            if (!$this->validator->validateType($value, $fieldType)) {
                throw new InvalidArgumentException($type . '.' . $field . ' type not valid.');
            }
        }
    }

    /**
     * @param array  &$prepared
     * @param string $type
     * @param string $field
     * @param array  $data
     * @param string $relation
     *
     * @return array
     */
    protected function prepareRelated(array &$prepared, $type, $field, array $data, $relation)
    {
        $relationInfos = $this->mapper->getRelationInfos($type, $field, $relation);
        if ($relationInfos) {
            $collection = $this->mapper->isCollection($relation);
            $targetEntity = $this->mapper->getTargetEntity($type, $field, $relation);
            $prepared[self::RELATED_KEY][$relation][$targetEntity][self::RELATED_RELATION_KEY] = $relationInfos;
            $relatedData = array();
            if ($collection) {
                foreach ($data as $element) {
                    $relatedData[] = $this->prepare($targetEntity, $element);
                }
            } else {
                $relatedData = $this->prepare($targetEntity, $data);
            }
            $prepared[self::RELATED_KEY][$relation][$targetEntity][self::RELATED_DATA_KEY] = $relatedData;
        }

        return $prepared;
    }

    /**
     * Last step of prepare which loop over entity mapping fields
     *
     * @param string $type entity name
     * @param array  $data entity data
     *
     * @return array
     */
    protected function checkFieldsMapping($type, array $data)
    {
        foreach ($this->mapper->getFieldsName($type) as $field) {
            if ($this->validator) {
                $this->checkNullable($type, $field, $data);
            }
            if ($fixedValue = $this->mapper->getFixedFieldMapping($type, $field)) {
                $data = array_merge($data, $fixedValue);
            }
        }

        return $data;
    }

    /**
     * @param string $type
     * @param string $field
     * @param array  $data
     *
     * @throws InvalidArgumentException
     */
    protected function checkNullable($type, $field, array $data)
    {
        if (!$this->mapper->isFieldNullable($type, $field)
            && !isset($data[$this->mapper->getFieldColumn($type, $field)])
        ) {
            throw new InvalidArgumentException($type . '.' . $field . ' is not nullable.');
        }
    }
}
