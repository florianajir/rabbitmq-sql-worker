<?php
namespace Meup\Bundle\SnotraBundle\Tests\DataTransformer;

use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformer;
use Meup\Bundle\SnotraBundle\DataValidator\DataValidator;
use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use PHPUnit_Framework_TestCase;

/**
 * Class DataTransformerTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class DataTransformerTest extends PHPUnit_Framework_TestCase
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
        $Mapper = new DataMapper($this->mapping);
        $DataTransformer = new DataTransformer($Mapper);
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
        $this->assertEquals($expected, $DataTransformer->prepare('user', $data));
    }

    /**
     *
     */
    public function testPrepareMissingNotNullableProperty()
    {
        $Mapper = new DataMapper($this->mapping);
        $Validator = new DataValidator();
        $DataTransformer = new DataTransformer($Mapper, $Validator);
        $data = array(
            'label' => 'label_de_test',
            'amount' => '10.01',
            'birthday' => '1989-11-10',
            'subscribe' => '2015-01-02T09:00:00+0200'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.identifier is not nullable.');
        $DataTransformer->prepare('user', $data);
    }

    /**
     *
     */
    public function testPrepareWrongType()
    {
        $Mapper = new DataMapper($this->mapping);
        $Validator = new DataValidator();
        $DataTransformer = new DataTransformer($Mapper, $Validator);

        $data = array(
            'identifier' => '1',
            'amount' => '10,01'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.amount type not valid.');
        $DataTransformer->prepare('user', $data);
        $data = array(
            'identifier' => '1',
            'birthday' => '11/10/1989'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.birthday type not valid.');
        $DataTransformer->prepare('user', $data);
        $data = array(
            'identifier' => '1',
            'subscribe' => '2015-01-02'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.subscribe type not valid.');
        $DataTransformer->prepare('user', $data);
    }

    /**
     * TODO
     */
    public function testPrepareRelationnal()
    {
//        $Mapper = new DataMapper($this->mapping);
//        $DataTransformer = new DataTransformer($Mapper);
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
