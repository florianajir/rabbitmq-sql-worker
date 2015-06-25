<?php
namespace Meup\Bundle\SnotraBundle\DataTransformer;

use DateTime;
use InvalidArgumentException;

/**
 * Class SqlDataTransformer
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SqlDataTransformer implements DataTransformerInterface
{
    const MAPPING_KEY_TABLE = 'table';
    const MAPPING_KEY_LENGTH = 'length';
    const MAPPING_KEY_COLUMN = 'column';
    const MAPPING_KEY_FIELDS = 'fields';
    const MAPPING_KEY_TYPE = 'type';
    const MAPPING_KEY_NULLABLE = 'nullable';

    const DATA_TYPE_STRING = 'string';
    const DATA_TYPE_INTEGER = 'int';
    const DATA_TYPE_DECIMAL = 'decimal';
    const DATA_TYPE_DATE = 'date';
    const DATA_TYPE_DATETIME = 'datetime';

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
     * Prepare data from mapping rules
     *
     * @param array  $data
     * @param string $type
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function prepare(array $data, $type)
    {
        $return = array();
        if (isset($this->mapping[$type])) {
            $tableName = $this->mapping[$type][self::MAPPING_KEY_TABLE];
            $return[$tableName] = array();
            foreach ($data as $field => $value) {
                if (isset($this->mapping[$type][self::MAPPING_KEY_FIELDS][$field])) {
                    $fieldMapping = $this->mapping[$type][self::MAPPING_KEY_FIELDS][$field];
                    // validate length if defined
                    if (isset($fieldMapping[self::MAPPING_KEY_LENGTH])
                        && !$this->validateLength($value, $fieldMapping[self::MAPPING_KEY_LENGTH])
                    ) {
                        throw new InvalidArgumentException($type . '.' . $field . ' value length exceed database max length.');
                    }
                    // validate type and nullable if defined
                    if (isset($fieldMapping[self::MAPPING_KEY_TYPE])
                        && !$this->validateType($value, $fieldMapping[self::MAPPING_KEY_TYPE])
                    ) {
                        throw new InvalidArgumentException($type . '.' . $field . ' type not valid.');
                    }
                    $return[$tableName][$fieldMapping[self::MAPPING_KEY_COLUMN]] = $value;
                }
            }

            // Check for not nullable fields
            foreach ($this->mapping[$type][self::MAPPING_KEY_FIELDS] as $properties) {
                $field = $properties[self::MAPPING_KEY_COLUMN];
                $nullable = !isset($properties[self::MAPPING_KEY_NULLABLE]) || $properties[self::MAPPING_KEY_NULLABLE] == 'true';
                if (empty($return[$tableName][$field]) && !$nullable) {
                    throw new InvalidArgumentException($type . '.' . $field . ' is not nullable.');
                }
            }
        }

        return $return;
    }

    /**
     * Check if value exceed max length defined in mapping
     *
     * @param string $value
     * @param int    $maxlength
     *
     * @return bool
     */
    protected function validateLength($value, $maxlength)
    {
        return strlen($value) <= $maxlength;
    }

    /**
     * Check if property type correspond to which declared in mapping
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return bool true if valid value type
     */
    protected function validateType($value, $type)
    {
        switch ($type) {
            case self::DATA_TYPE_INTEGER:
                return is_numeric($value) && is_int(intval($value));
            case self::DATA_TYPE_DECIMAL:
                return is_numeric($value) && is_float(floatval($value));
            case self::DATA_TYPE_DATETIME:
                return $this->validateDate($value, DateTime::ISO8601);
            case self::DATA_TYPE_DATE:
                return $this->validateDate($value, 'Y-m-d');
            case self::DATA_TYPE_STRING:
            default:
                return is_string($value);
        }
    }

    /**
     * Check the validity of a date or datetime format
     *
     * @param string $date
     * @param string $format
     *
     * @return bool
     */
    protected function validateDate($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

}
