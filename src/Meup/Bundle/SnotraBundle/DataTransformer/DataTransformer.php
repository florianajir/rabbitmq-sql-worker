<?php
namespace Meup\Bundle\SnotraBundle\DataTransformer;

use InvalidArgumentException;
use Meup\Bundle\SnotraBundle\DataValidator\DataValidatorInterface;
use Meup\Bundle\SnotraBundle\DataMapper\DataMapperInterface;

/**
 * Class DataTransformer
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class DataTransformer implements DataTransformerInterface
{
    const RELATED_KEY = '_related';
    const RELATED_INFOS_KEY = '_relation';
    const RELATED_DATA_KEY = '_data';

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
                if (!empty($fieldMapping)) {
                    if ($this->validator) {
                        $this->validate($type, $field, $value);
                    }
                    $fieldColumn = $this->mapper->getFieldColumn($type, $field);
                    $prepared[$tableName][$fieldColumn] = $value;
                } elseif ($relation) {
                    $relationInfos = $this->mapper->getRelationInfos($type, $field, $relation);
                    if ($relationInfos) {
                        $collection = $this->mapper->relationExpectCollection($relation);
                        $targetEntity = $this->mapper->getTargetEntity($type, $field, $relation);
                        if ($collection) {
                            foreach ($value as $element) {
                                $prepared[$tableName][self::RELATED_KEY][$relation][] = array(
                                    self::RELATED_INFOS_KEY => $relationInfos,
                                    self::RELATED_DATA_KEY  => $this->prepare($targetEntity, $element)
                                );
                            }
                        } else {
                            $prepared[$tableName][self::RELATED_KEY][$relation][] = array(
                                self::RELATED_INFOS_KEY => $relationInfos,
                                self::RELATED_DATA_KEY  => $this->prepare($targetEntity, $value)
                            );
                        }
                    }
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
    public function validate($type, $field, $value)
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
     * @param string $type
     * @param array  $data
     *
     * @throws InvalidArgumentException
     */
    public function checkNullable($type, $data)
    {
        $fields = $this->mapper->getFieldsName($type);
        foreach ($fields as $field) {
            if (
                !$this->mapper->getFieldNullable($type, $field)
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
