<?php
namespace Meup\Bundle\SnotraBundle\DataValidator;

use DateTime;

/**
 * Class DataValidator
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class DataValidator implements DataValidatorInterface
{
    const DATA_TYPE_STRING = 'string';
    const DATA_TYPE_INTEGER = 'int';
    const DATA_TYPE_DECIMAL = 'decimal';
    const DATA_TYPE_DATE = 'date';
    const DATA_TYPE_DATETIME = 'datetime';

    /**
     * Check if value exceed max length defined in mapping
     *
     * @param string $value
     * @param int    $maxlength
     *
     * @return bool
     */
    public function validateLength($value, $maxlength)
    {
        return strlen($value) <= $maxlength;
    }

    /**
     * Check if property type correspond to which declared in mapping
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return bool true if valid value type or null value
     */
    public function validateType($value, $type)
    {
        if (!is_null($value)) {
            switch ($type) {
                case self::DATA_TYPE_INTEGER:
                    return $this->isInteger($value);
                case self::DATA_TYPE_DECIMAL:
                    return $this->isFloat($value);
                case self::DATA_TYPE_DATETIME:
                    return $this->validateDate($value, DateTime::ISO8601);
                case self::DATA_TYPE_DATE:
                    return $this->validateDate($value, 'Y-m-d');
                case self::DATA_TYPE_STRING:
                    return is_string($value);
            }
        }

        return true;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function isInteger($input)
    {
        return (ctype_digit(strval($input)));
    }

    /**
     * test if parameter is containing a float
     *
     * @param mixed $f
     *
     * @return bool
     */
    public function isFloat($f)
    {
        return ($f == (string)(float)$f);
    }

    /**
     * Check the validity of a date or datetime format
     *
     * @param string $date
     * @param string $format
     *
     * @return bool
     */
    public function validateDate($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}
