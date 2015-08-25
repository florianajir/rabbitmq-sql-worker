<?php
namespace Meup\Bundle\SnotraBundle\DataValidator;

/**
 * Interface DataValidatorInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface DataValidatorInterface
{
    /**
     * Check if value exceed max length defined in mapping
     *
     * @param string $value
     * @param int    $maxlength
     *
     * @return bool if valid length
     */
    public function validateLength($value, $maxlength);

    /**
     * Check if property type correspond to which declared in mapping
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return bool true if valid value type
     */
    public function validateType($value, $type);

    /**
     * Check the validity of a date or datetime format
     *
     * @param string $date
     * @param string $format
     *
     * @return bool if valid date
     */
    public function validateDate($date, $format);
}
