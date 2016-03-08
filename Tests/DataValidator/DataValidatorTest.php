<?php
namespace Meup\Bundle\SnotraBundle\Tests\DataValidator;

use DateTime;
use PHPUnit_Framework_TestCase;
use Meup\Bundle\SnotraBundle\DataValidator\DataValidator;

/**
 * Class DataValidatorTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class DataValidatorTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testValidateCorrectDateTime()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateDate('2015-06-26T22:22:00+0200', DateTime::ISO8601);
        $this->assertTrue($valid);
    }

    /**
     *
     */
    public function testValidateWrongDateTime()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateDate('2015-06-26T22:22:00+02:00', DateTime::ISO8601);
        $this->assertFalse($valid);
    }

    /**
     *
     */
    public function testValidateCorrectDate()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateDate('2015-06-26', 'Y-m-d');
        $this->assertTrue($valid);
    }

    /**
     *
     */
    public function testValidateWrongDate()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateDate('23/06/2015', 'Y-m-d');
        $this->assertFalse($valid);
    }

    /**
     *
     */
    public function testValidateCorrectLength()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateLength('0123456789', 20);
        $this->assertTrue($valid);
    }

    /**
     *
     */
    public function testValidateWrongLength()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateLength('0123456789', 5);
        $this->assertFalse($valid);
    }

    /**
     *
     */
    public function testValidateInt()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateType(123456789, DataValidator::DATA_TYPE_INTEGER);
        $this->assertTrue($valid);
        $valid = $Validator->validateType('123456789', DataValidator::DATA_TYPE_INTEGER);
        $this->assertTrue($valid);
        $valid = $Validator->validateType('azerty', DataValidator::DATA_TYPE_INTEGER);
        $this->assertFalse($valid);
        $valid = $Validator->validateType('12345.6789', DataValidator::DATA_TYPE_INTEGER);
        $this->assertFalse($valid);
    }

    /**
     *
     */
    public function testValidateFloat()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateType(123456789, DataValidator::DATA_TYPE_DECIMAL);
        $this->assertTrue($valid);
        $valid = $Validator->validateType('123456789', DataValidator::DATA_TYPE_DECIMAL);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(123456.789, DataValidator::DATA_TYPE_DECIMAL);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(1e7, DataValidator::DATA_TYPE_DECIMAL);
        $this->assertTrue($valid);
        $valid = $Validator->validateType('123456.789', DataValidator::DATA_TYPE_DECIMAL);
        $this->assertTrue($valid);
        $valid = $Validator->validateType('azerty', DataValidator::DATA_TYPE_DECIMAL);
        $this->assertFalse($valid);
        $valid = $Validator->validateType('12345,6789', DataValidator::DATA_TYPE_DECIMAL);
        $this->assertFalse($valid);
    }

    /**
     *
     */
    public function testValidateString()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateType(123456789, DataValidator::DATA_TYPE_STRING);
        $this->assertFalse($valid);
        $valid = $Validator->validateType('123456789', DataValidator::DATA_TYPE_STRING);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(123456.789, DataValidator::DATA_TYPE_STRING);
        $this->assertFalse($valid);
        $valid = $Validator->validateType(1e7, DataValidator::DATA_TYPE_STRING);
        $this->assertFalse($valid);
        $valid = $Validator->validateType('123456.789', DataValidator::DATA_TYPE_STRING);
        $this->assertTrue($valid);
        $valid = $Validator->validateType('azerty', DataValidator::DATA_TYPE_STRING);
        $this->assertTrue($valid);
        $valid = $Validator->validateType('12345,6789', DataValidator::DATA_TYPE_STRING);
        $this->assertTrue($valid);
    }

    /**
     *
     */
    public function testValidateNullValues()
    {
        $Validator = new DataValidator();
        $valid = $Validator->validateType(null, DataValidator::DATA_TYPE_STRING);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(null, DataValidator::DATA_TYPE_DATE);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(null, DataValidator::DATA_TYPE_DATETIME);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(null, DataValidator::DATA_TYPE_DECIMAL);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(null, DataValidator::DATA_TYPE_INTEGER);
        $this->assertTrue($valid);
        $valid = $Validator->validateType(null, null);
        $this->assertTrue($valid);
    }
}
