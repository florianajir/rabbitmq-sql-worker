<?php
namespace Meup\Bundle\SnotraBundle\Tests\DataMapper;

use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use PHPUnit_Framework_TestCase;

/**
 * Class DataMapperTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class DataMapperTest extends PHPUnit_Framework_TestCase
{

    /**
     * The simple mapping fixture
     *
     * @var array
     */
    protected $mapping;

    /**
     * The relationnal mapping fixture
     *
     * @var array
     */
    protected $relationnalMapping;

    /**
     *
     */
    public function testGetTargetEntity()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = 'Group';
        $this->assertEquals($expected, $Mapper->getTargetEntity('User', 'Groups', 'manyToMany'));
    }

    /**
     *
     */
    public function testGetFieldColumn()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = 'identifier';
        $this->assertEquals($expected, $Mapper->getFieldColumn('Customer', 'sku'));
    }

    /**
     *
     */
    public function testGetFieldMaxLength()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = 23;
        $this->assertEquals($expected, $Mapper->getFieldMaxLength('Customer', 'sku'));
    }

    /**
     *
     */
    public function testGetFieldsName()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = array('sku', 'parent_id');
        $this->assertEquals($expected, $Mapper->getFieldsName('User'));
    }

    /**
     *
     */
    public function testGetFieldNullable()
    {
        $Mapper = new DataMapper($this->mapping);
        $this->assertFalse($Mapper->isFieldNullable('User', 'sku'));
        $this->assertTrue($Mapper->isFieldNullable('User', 'parent_id'));
    }

    /**
     *
     */
    public function testGetFieldType()
    {
        $Mapper = new DataMapper($this->mapping);
        $this->assertEquals('int', $Mapper->getFieldType('User', 'parent_id'));
    }

    /**
     *
     */
    public function testGetFieldMapping()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = array(
            'column'   => 'sku',
            'length'   => 23,
            'type'     => 'string',
            'nullable' => false,
        );
        $this->assertEquals(
            $expected,
            $Mapper->getFieldMapping('User', 'sku')
        );
    }

    /**
     *
     */
    public function testGetRelation()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = 'oneToOne';
        $this->assertEquals(
            $expected,
            $Mapper->getRelation('User', 'Customer')
        );
    }

    /**
     *
     */
    public function testGetTableName()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = 'customer';
        $this->assertEquals(
            $expected,
            $Mapper->getTableName('Customer')
        );
    }

    /**
     *
     */
    public function testGetIdentifier()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = 'sku';
        $this->assertEquals(
            $expected,
            $Mapper->getIdentifier('User')
        );
    }

    /**
     *
     */
    public function testGetRelationInfos()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = array(
            'joinColumn'   =>
                array(
                    'referencedColumnName' => 'id',
                    'name'                 => 'customer_id',
                ),
            'targetEntity' => 'Customer',
            'table'        => 'customer',
        );
        $this->assertEquals(
            $expected,
            $Mapper->getRelationInfos('User', 'Customer', 'oneToOne')
        );
    }

    /**
     *
     */
    public function testGetJoinTable()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = array(
            'joinColumns'        =>
                array(
                    'user_id' =>
                        array(
                            'referencedColumnName' => 'id',
                        ),
                ),
            'name'               => 'users_groups',
            'inverseJoinColumns' =>
                array(
                    'group_id' =>
                        array(
                            'referencedColumnName' => 'id',
                        ),
                ),
        );
        $this->assertEquals(
            $expected,
            $Mapper->getJoinTable('User', 'Groups', 'manyToMany')
        );
    }

    /**
     *
     */
    public function testRelationExpectCollection()
    {
        $Mapper = new DataMapper($this->mapping);
        $expected = true;
        $this->assertEquals(
            $expected,
            $Mapper->relationExpectCollection('manyToMany')
        );
        $expected = true;
        $this->assertEquals(
            $expected,
            $Mapper->relationExpectCollection('oneToMany')
        );
        $expected = false;
        $this->assertEquals(
            $expected,
            $Mapper->relationExpectCollection('oneToOne')
        );
        $expected = false;
        $this->assertEquals(
            $expected,
            $Mapper->relationExpectCollection('manyToOne')
        );
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->mapping = array(
            'Customer' =>
                array(
                    'table'  => 'customer',
                    'fields' =>
                        array(
                            'sku' =>
                                array(
                                    'column'   => 'identifier',
                                    'length'   => 23,
                                    'type'     => 'string',
                                    'nullable' => false,
                                )
                        ),
                ),
            'Group'    =>
                array(
                    'table'  => 'group',
                    'fields' =>
                        array(
                            'sku' =>
                                array(
                                    'column'   => 'sku',
                                    'length'   => 23,
                                    'type'     => 'string',
                                    'nullable' => false,
                                ),
                        ),
                ),
            'User'     =>
                array(
                    'oneToOne'   =>
                        array(
                            'Customer' =>
                                array(
                                    'joinColumn'   =>
                                        array(
                                            'referencedColumnName' => 'id',
                                            'name'                 => 'customer_id',
                                        ),
                                    'targetEntity' => 'Customer',
                                ),
                        ),
                    'oneToMany'  =>
                        array(
                            'Children' =>
                                array(
                                    'joinColumn'   =>
                                        array(
                                            'referencedColumnName' => 'id',
                                            'name'                 => 'parent_id',
                                        ),
                                    'targetEntity' => 'User',
                                ),
                        ),
                    'fields'     =>
                        array(
                            'sku'       =>
                                array(
                                    'column'   => 'sku',
                                    'length'   => 23,
                                    'type'     => 'string',
                                    'nullable' => false,
                                ),
                            'parent_id' =>
                                array(
                                    'column'   => 'parent_id',
                                    'type'     => 'int',
                                    'nullable' => true,
                                ),
                        ),
                    'manyToOne'  =>
                        array(
                            'Address' =>
                                array(
                                    'joinColumn'   =>
                                        array(
                                            'referencedColumnName' => 'id',
                                            'name'                 => 'address_id',
                                        ),
                                    'targetEntity' => 'Address',
                                ),
                        ),
                    'table'      => 'user',
                    'identifier' => 'sku',
                    'manyToMany' =>
                        array(
                            'Groups' =>
                                array(
                                    'joinTable'    =>
                                        array(
                                            'joinColumns'        =>
                                                array(
                                                    'user_id' =>
                                                        array(
                                                            'referencedColumnName' => 'id',
                                                        ),
                                                ),
                                            'name'               => 'users_groups',
                                            'inverseJoinColumns' =>
                                                array(
                                                    'group_id' =>
                                                        array(
                                                            'referencedColumnName' => 'id',
                                                        ),
                                                ),
                                        ),
                                    'targetEntity' => 'Group',
                                ),
                        ),
                ),
            'Address'  =>
                array(
                    'table'  => 'address',
                    'fields' =>
                        array(
                            'sku' =>
                                array(
                                    'column'   => 'sku',
                                    'length'   => 255,
                                    'type'     => 'string',
                                    'nullable' => false
                                ),
                        ),
                ),
        );
    }
}
