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
        $mapper = new DataMapper($this->mapping);
        $expected = 'Group';
        $this->assertEquals($expected, $mapper->getTargetEntity('User', 'Groups', 'manyToMany'));
    }

    /**
     *
     */
    public function testGetFieldColumn()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = 'identifier';
        $this->assertEquals($expected, $mapper->getFieldColumn('Customer', 'sku'));
    }

    /**
     *
     */
    public function testGetFieldMaxLength()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = 23;
        $this->assertEquals($expected, $mapper->getFieldMaxLength('Customer', 'sku'));
    }

    /**
     *
     */
    public function testGetFieldsName()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = array('sku', 'parent_id');
        $this->assertEquals($expected, $mapper->getFieldsName('User'));
    }

    /**
     *
     */
    public function testGetFieldNullable()
    {
        $mapper = new DataMapper($this->mapping);
        $this->assertFalse($mapper->isFieldNullable('User', 'sku'));
        $this->assertTrue($mapper->isFieldNullable('User', 'parent_id'));
    }

    /**
     *
     */
    public function testGetFieldType()
    {
        $mapper = new DataMapper($this->mapping);
        $this->assertEquals('int', $mapper->getFieldType('User', 'parent_id'));
    }

    /**
     *
     */
    public function testGetFieldMapping()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = array(
            'column'   => 'sku',
            'length'   => 23,
            'type'     => 'string',
            'nullable' => false,
        );
        $this->assertEquals(
            $expected,
            $mapper->getFieldMapping('User', 'sku')
        );
    }

    /**
     *
     */
    public function testGetRelation()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = 'oneToOne';
        $this->assertEquals(
            $expected,
            $mapper->getRelation('User', 'Customer')
        );
    }

    /**
     *
     */
    public function testGetTableName()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = 'customer';
        $this->assertEquals(
            $expected,
            $mapper->getTableName('Customer')
        );
    }

    /**
     *
     */
    public function testGetIdentifier()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = 'sku';
        $this->assertEquals(
            $expected,
            $mapper->getIdentifier('User')
        );
    }

    /**
     *
     */
    public function testGetRelationInfos()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = array(
            'joinColumn'   => array(
                'referencedColumnName' => 'id',
                'name'                 => 'customer_id',
            ),
            'targetEntity' => 'Customer',
            'table'        => 'customer',
        );
        $this->assertEquals(
            $expected,
            $mapper->getRelationInfos('User', 'Customer', 'oneToOne')
        );
    }

    /**
     *
     */
    public function testGetJoinTable()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = array(
            'joinColumns'        => array(
                'user_id' => array(
                    'referencedColumnName' => 'id',
                ),
            ),
            'name'               => 'users_groups',
            'inverseJoinColumns' => array(
                'group_id' => array(
                    'referencedColumnName' => 'id',
                ),
            ),
        );
        $this->assertEquals(
            $expected,
            $mapper->getJoinTable('User', 'Groups', 'manyToMany')
        );
    }

    /**
     *
     */
    public function testRelationExpectCollection()
    {
        $mapper = new DataMapper($this->mapping);
        $expected = true;
        $this->assertEquals(
            $expected,
            $mapper->isCollection('manyToMany')
        );
        $expected = true;
        $this->assertEquals(
            $expected,
            $mapper->isCollection('oneToMany')
        );
        $expected = false;
        $this->assertEquals(
            $expected,
            $mapper->isCollection('oneToOne')
        );
        $expected = false;
        $this->assertEquals(
            $expected,
            $mapper->isCollection('manyToOne')
        );
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->mapping = array(
            'Customer' => array(
                'table'  => 'customer',
                'fields' =>
                    array(
                        'sku' => array(
                            'column'   => 'identifier',
                            'length'   => 23,
                            'type'     => 'string',
                            'nullable' => false,
                        )
                    ),
            ),
            'Group'    => array(
                'table'  => 'group',
                'fields' => array(
                    'sku' => array(
                        'column'   => 'sku',
                        'length'   => 23,
                        'type'     => 'string',
                        'nullable' => false,
                    ),
                ),
            ),
            'User'     => array(
                'oneToOne'   => array(
                    'Customer' => array(
                        'joinColumn'   => array(
                            'referencedColumnName' => 'id',
                            'name'                 => 'customer_id',
                        ),
                        'targetEntity' => 'Customer',
                    ),
                ),
                'oneToMany'  => array(
                    'Children' => array(
                        'joinColumn'   => array(
                            'referencedColumnName' => 'id',
                            'name'                 => 'parent_id',
                        ),
                        'targetEntity' => 'User',
                    ),
                ),
                'fields'     => array(
                    'sku'       => array(
                        'column'   => 'sku',
                        'length'   => 23,
                        'type'     => 'string',
                        'nullable' => false,
                    ),
                    'parent_id' => array(
                        'column'   => 'parent_id',
                        'type'     => 'int',
                        'nullable' => true,
                    ),
                ),
                'manyToOne'  => array(
                    'Address' => array(
                        'joinColumn'   => array(
                            'referencedColumnName' => 'id',
                            'name'                 => 'address_id',
                        ),
                        'targetEntity' => 'Address',
                    ),
                ),
                'table'      => 'user',
                'identifier' => 'sku',
                'manyToMany' => array(
                    'Groups' => array(
                        'joinTable'    => array(
                            'joinColumns'        => array(
                                'user_id' => array(
                                    'referencedColumnName' => 'id',
                                ),
                            ),
                            'name'               => 'users_groups',
                            'inverseJoinColumns' => array(
                                'group_id' => array(
                                    'referencedColumnName' => 'id',
                                ),
                            ),
                        ),
                        'targetEntity' => 'Group',
                    ),
                ),
            ),
            'Address'  => array(
                'table'  => 'address',
                'fields' => array(
                    'sku' => array(
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
