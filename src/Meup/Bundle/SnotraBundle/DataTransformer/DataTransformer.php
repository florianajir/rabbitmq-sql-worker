<?php
namespace Meup\Bundle\SnotraBundle\DataTransformer;

use InvalidArgumentException;
use Meup\Bundle\SnotraBundle\DataMapper\DataMapperInterface;
use Meup\Bundle\SnotraBundle\DataValidator\DataValidatorInterface;

/**
 * Class DataTransformer
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class DataTransformer implements DataTransformerInterface
{
    const RELATED_KEY = '_related';
    const RELATED_RELATION_KEY = '_relation';
    const RELATED_DATA_KEY = '_data';
    const IDENTIFIER_KEY = '_identifier';

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
        $tableName = $this->mapper->getTableName($type);
        if ($tableName) {
            $prepared[$tableName] = array();
            foreach ($data as $field => $value) {
                $fieldMapping = $this->mapper->getFieldMapping($type, $field);
                $relation = $this->mapper->getRelation($type, $field);
                $prepared[$tableName][self::IDENTIFIER_KEY] = $this->mapper->getIdentifier($type);
                if (!empty($fieldMapping)) {
                    if ($this->validator) {
                        $this->validate($type, $field, $value);
                    }
                    $fieldColumn = $this->mapper->getFieldColumn($type, $field);
                    $prepared[$tableName][$fieldColumn] = $value;
                } elseif ($relation) {
                    $this->prepareRelated($prepared, $type, $field, $value, $relation, $tableName);
                }
            }
            if ($this->validator) {
                $this->checkNullable($type, $prepared[$tableName]);
            }
        }

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
     * @param array  $prepared
     * @param string $type
     * @param string $field
     * @param string|array $value
     * @param string $relation
     * @param string $tableName
     */
    protected function prepareRelated(&$prepared, $type, $field, $value, $relation, $tableName)
    {
        $relationInfos = $this->mapper->getRelationInfos($type, $field, $relation);
        if ($relationInfos) {
            $collection = $this->mapper->relationExpectCollection($relation);
            $targetEntity = $this->mapper->getTargetEntity($type, $field, $relation);
            $linkedTableName = $this->mapper->getTableName($type);
            $prepared[$tableName][self::RELATED_KEY][$relation][$linkedTableName][self::RELATED_RELATION_KEY] = $relationInfos;
            if ($collection) {
                foreach ($value as $element) {
                    $prepared[$tableName][self::RELATED_KEY][$relation][$linkedTableName][self::RELATED_DATA_KEY][] = $this->prepare($targetEntity, $element);
                }
            } else {
                $prepared[$tableName][self::RELATED_KEY][$relation][$linkedTableName][self::RELATED_DATA_KEY] = $this->prepare($targetEntity, $value);
            }
        }
    }

    /**
     * @param string $type
     * @param array  $data
     *
     * @throws InvalidArgumentException
     */
    protected function checkNullable($type, $data)
    {
        $fields = $this->mapper->getFieldsName($type);
        foreach ($fields as $field) {
            if (!$this->mapper->getFieldNullable($type, $field)
                && (
                    !isset($data[$this->mapper->getFieldColumn($type, $field)])
                    || is_null($data[$this->mapper->getFieldColumn($type, $field)])
                )
            ) {
                throw new InvalidArgumentException($type . '.' . $field . ' is not nullable.');
            }
        }
    }
}
