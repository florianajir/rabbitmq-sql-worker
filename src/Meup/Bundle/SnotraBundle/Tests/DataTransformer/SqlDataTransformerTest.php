<?php
namespace Meup\Bundle\SnotraBundle\Tests\DataTransformer;

use Meup\Bundle\SnotraBundle\DataTransformer\SqlDataTransformer;
use PHPUnit_Framework_TestCase;

/**
 * Class SqlDataTransformerTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SqlDataTransformerTest extends PHPUnit_Framework_TestCase
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
     * The message data
     *
     * @var array
     */
    protected $data;

    /**
     *
     */
    public function testPrepare()
    {
        $sqlDataTransformer = new SqlDataTransformer($this->mapping);
        $data = array(
            'identifier' => '1',
            'label' => 'label_de_test',
            'amount' => '10.01',
            'birthday' => '1989-11-10',
            'subscribe' => '2015-01-02T09:00:00+0200'
        );
        $expected = array(
            'users' => array(
                'id' => '1',
                'name' => 'label_de_test',
                'amount' => '10.01',
                'birthdate' => '1989-11-10',
                'created_at' => '2015-01-02T09:00:00+0200'
            )
        );
        $this->assertEquals($expected, $sqlDataTransformer->prepare($data, 'user'));
    }

    /**
     *
     */
    public function testPrepareMissingNotNullableProperty()
    {
        $sqlDataTransformer = new SqlDataTransformer($this->mapping);
        $data = array(
            'label' => 'label_de_test',
            'amount' => '10.01',
            'birthday' => '1989-11-10',
            'subscribe' => '2015-01-02T09:00:00+0200'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.id is not nullable.');
        $sqlDataTransformer->prepare($data, 'user');
    }

    /**
     *
     */
    public function testPrepareWrongType()
    {
        $sqlDataTransformer = new SqlDataTransformer($this->mapping);
        $data = array(
            'identifier' => '1',
            'amount' => '10,01'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.amount type not valid.');
        $sqlDataTransformer->prepare($data, 'user');
        $data = array(
            'identifier' => '1',
            'birthday' => '11/10/1989'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.birthday type not valid.');
        $sqlDataTransformer->prepare($data, 'user');
        $data = array(
            'identifier' => '1',
            'subscribe' => '2015-01-02'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.subscribe type not valid.');
        $sqlDataTransformer->prepare($data, 'user');
    }

    /**
     * TODO
     */
    public function testPrepareRelationnal()
    {
//        $sqlDataTransformer = new SqlDataTransformer($this->relationnalMapping);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->mapping = array(
            'user' => array(
                'table'  => 'users',
                'fields' => array(
                    'identifier' => array(
                        'column'   => 'id',
                        'type'     => 'int',
                        'length'   => '8',
                        'nullable' => 'false'
                    ),
                    'label'      => array(
                        'column' => 'name',
                        'type'   => 'string',
                    ),
                    'amount'     => array(
                        'column' => 'amount',
                        'type'   => 'decimal',
                    ),
                    'birthday'   => array(
                        'column' => 'birthdate',
                        'type'   => 'date',
                    ),
                    'subscribe'  => array(
                        'column' => 'created_at',
                        'type'   => 'datetime',
                    ),
                    'missing'    => array(
                        'column'   => 'missing',
                        'type'     => 'string',
                        'nullable' => 'true'
                    )
                )
            )
        );
    }

}
