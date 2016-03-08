<?php
namespace Ajir\RabbitMqSqlBundle\Tests\DataTransformer;

use Ajir\RabbitMqSqlBundle\DataMapper\DataMapper;
use Ajir\RabbitMqSqlBundle\DataTransformer\DataTransformer;
use Ajir\RabbitMqSqlBundle\DataValidator\DataValidator;
use Ajir\RabbitMqSqlBundle\Model\Entity;
use PHPUnit_Framework_TestCase;

/**
 * Class DataTransformerTest
 *
 * @author Florian Ajir <florianajir@gmail.com>
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
     * The relationnal collection mapping fixture
     *
     * @var array
     */
    protected $relationnalCollectionMapping;

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
        $mapper = new DataMapper($this->mapping);
        $dataTransformer = new DataTransformer($mapper);
        $data = array(
            'identifier' => '1',
            'label'      => 'label_de_test',
            'amount'     => '10.01',
            'birthday'   => '1989-11-10',
            'subscribe'  => '2015-01-02T09:00:00+0200'
        );
        $expected = array(
            'user' => array(
                'id'          => '1',
                'name'        => 'label_de_test',
                'amount'      => '10.01',
                'birthdate'   => '1989-11-10',
                'created_at'  => '2015-01-02T09:00:00+0200',
                '_identifier' => 'id',
                '_table'      => 'users'
            )
        );
        $this->assertEquals($expected, $dataTransformer->prepare('user', $data));
    }

    /**
     *
     */
    public function testPrepareMissingNotNullableProperty()
    {
        $mapper = new DataMapper($this->mapping);
        $validator = new DataValidator();
        $dataTransformer = new DataTransformer($mapper, $validator);
        $data = array(
            'label'     => 'label_de_test',
            'amount'    => '10.01',
            'birthday'  => '1989-11-10',
            'subscribe' => '2015-01-02T09:00:00+0200'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.identifier is not nullable.');
        $dataTransformer->prepare('user', $data);
    }

    /**
     *
     */
    public function testPrepareMissingNullableProperty()
    {
        $mapper = new DataMapper($this->mapping);
        $validator = new DataValidator();
        $dataTransformer = new DataTransformer($mapper, $validator);
        $data = array(
            'identifier' => '1234567',
        );
        $result = $dataTransformer->prepare('user', $data);
        $this->assertEquals(
            array(
                'user' => array(
                    '_identifier' => "id",
                    '_table'      => "users",
                    'id'          => "1234567"
                )
            ),
            $result
        );
    }

    /**
     *
     */
    public function testPrepareWrongType()
    {
        $mapper = new DataMapper($this->mapping);
        $validator = new DataValidator();
        $dataTransformer = new DataTransformer($mapper, $validator);

        $data = array(
            'identifier' => '1',
            'amount'     => '10,01'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.amount type not valid.');
        $dataTransformer->prepare('user', $data);
        $data = array(
            'identifier' => '1',
            'birthday'   => '11/10/1989'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.birthday type not valid.');
        $dataTransformer->prepare('user', $data);
        $data = array(
            'identifier' => '1',
            'subscribe'  => '2015-01-02'
        );
        $this->setExpectedException('InvalidArgumentException', 'user.subscribe type not valid.');
        $dataTransformer->prepare('user', $data);
    }

    /**
     *
     */
    public function testPrepareLengthExceed()
    {
        $mapper = new DataMapper($this->mapping);
        $validator = new DataValidator();
        $dataTransformer = new DataTransformer($mapper, $validator);

        $data = array(
            'identifier' => '123456789'
        );
        $this->setExpectedException(
            'InvalidArgumentException',
            'user.identifier value length exceed database max length.'
        );
        $dataTransformer->prepare('user', $data);
    }

    /**
     *
     */
    public function testPrepareRelationnal()
    {
        $mapper = new DataMapper($this->relationnalMapping);
        $dataTransformer = new DataTransformer($mapper);
        $data = array(
            'identifier' => '1',
            'label'      => 'label_de_test',
            'Address'    => array(
                'identifier'  => '2',
                'postal_code' => '34000',
                'city'        => 'Montpellier'
            )
        );
        $expected = array(
            'User' => array(
                'sku'         => '1',
                '_identifier' => 'sku',
                '_table'      => 'users',
                '_related'    => array(
                    'manyToOne' => array(
                        'Address' => array(
                            '_relation' => array(
                                'targetEntity' => 'Address',
                                'joinColumn'   => array(
                                    'name'                 => 'address_id',
                                    'referencedColumnName' => 'id'
                                ),
                                'table'        => 'address'
                            ),
                            '_data'     => array(
                                'Address' => array(
                                    '_identifier' => 'sku',
                                    '_table'      => 'address',
                                    'sku'         => '2',
                                    'postal_code' => '34000',
                                    'city'        => 'Montpellier'
                                )
                            )
                        )
                    )
                )
            )
        );
        $result = $dataTransformer->prepare('User', $data);
        $this->assertEquals($expected, $result);
    }

    /**
     *
     */
    public function testPrepareWithDiscriminator()
    {
        $mapper = new DataMapper(array(
            'supplier' => array(
                'discriminator' => 'dtype',
                'fields'        => array(
                    'sku' => array(
                        'column' => 'sku',
                        'type'   => 'string',
                    )
                )
            )
        ));
        $dataTransformer = new DataTransformer($mapper);
        $data = array(
            'sku'   => '1234567',
            'dtype' => 'brand'
        );
        $expected = array(
            'supplier' => array(
                'sku'    => '1234567',
                '_table' => 'brand'
            )
        );
        $result = $dataTransformer->prepare('supplier', $data);
        $this->assertEquals($expected, $result);

        $entity = new Entity($result['supplier']);
        $this->assertEquals('brand', $entity->getTable());
    }

    /**
     *
     */
    public function testValidateLengthExceed()
    {
        $mapper = new DataMapper(array(
            'user' => array(
                'table'  => 'user',
                'fields' => array(
                    'sku' => array(
                        'column' => 'sku',
                        'type'   => 'string',
                        'length' => '7'
                    )
                )
            )
        ));
        $dataTransformer = new DataTransformer($mapper, new DataValidator());
        $data = array(
            'sku' => '12345678'
        );
        $this->setExpectedException('InvalidArgumentException');
        $dataTransformer->prepare('user', $data);
    }

    /**
     *
     */
    public function testPrepareRelationnalCollection()
    {
        $mapper = new DataMapper($this->relationnalCollectionMapping);
        $dataTransformer = new DataTransformer($mapper);
        $data = array(
            'sku' => '1234567',
            'fr'  => array(
                array(
                    'name' => 'test'
                )
            )
        );
        $expected = array(
            'selection' => array(
                'sku'         => '1234567',
                '_identifier' => 'sku',
                '_table'      => 'selection',
                '_related'    => array(
                    'oneToMany' => array(
                        'selection_lang' => array(
                            '_relation' => array(
                                'targetEntity' => 'selection_lang',
                                'joinColumn'   => array(
                                    'referencedColumnName' => 'id',
                                    'name'                 => 'selection_id',
                                ),
                                'references'   => array(
                                    'lang_id' => array(
                                        'table'                => 'lang',
                                        'referencedColumnName' => 'id',
                                        'where'                => array(
                                            'iso_code' => 'fr'
                                        )
                                    )
                                ),
                                'table'        => 'selection_lang'
                            ),
                            '_data'     => array(
                                array(
                                    'selection_lang' => array(
                                        '_table'      => 'selection_lang',
                                        '_identifier' => 'name',
                                        'name'        => 'test',
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        $result = $dataTransformer->prepare('selection', $data);
        $this->assertEquals($expected, $result);
    }

    /**
     *
     */
    public function testPrepareWithFixedFieldMapping()
    {
        $mapper = new DataMapper(array(
            'supplier' => array(
                'fields' => array(
                    'sku'  => array(
                        'column' => 'sku',
                    ),
                    'type' => array(
                        'column' => 'type',
                        'value'  => 'brand'
                    )
                )
            )
        ));
        $dataTransformer = new DataTransformer($mapper);
        $data = array(
            'sku' => '1234567'
        );
        $expected = array(
            'supplier' => array(
                'sku'  => '1234567',
                'type' => 'brand'
            )
        );
        $result = $dataTransformer->prepare('supplier', $data);
        $this->assertEquals($expected, $result);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->setUpMapping();
        $this->setUpRelationnalMapping();
        $this->setUpRelationnalCollectionMapping();
    }

    private function setUpMapping()
    {
        $this->mapping = array(
            'user' => array(
                'table'      => 'users',
                'identifier' => 'id',
                'fields'     => array(
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

    private function setUpRelationnalMapping()
    {
        $this->relationnalMapping = array(
            'User'    => array(
                'table'      => 'users',
                'identifier' => 'sku',
                'fields'     => array(
                    'identifier' => array(
                        'column'   => 'sku',
                        'type'     => 'int',
                        'length'   => '8',
                        'nullable' => 'false'
                    )
                ),
                'manyToOne'  =>
                    array(
                        'Address' =>
                            array(
                                'joinColumn'   => array(
                                    'referencedColumnName' => 'id',
                                    'name'                 => 'address_id',
                                ),
                                'targetEntity' => 'Address',
                            ),
                    )
            ),
            'Address' => array(
                'table'      => 'address',
                'identifier' => 'sku',
                'fields'     => array(
                    'identifier'  => array(
                        'column' => 'sku',
                        'type'   => 'string'
                    ),
                    'postal_code' => array(
                        'column' => 'postal_code',
                        'type'   => 'string',
                        'length' => '5'
                    ),
                    'city'        => array(
                        'column' => 'city',
                        'type'   => 'string',
                    ),
                )
            )
        );
    }

    /**
     *
     */
    private function setUpRelationnalCollectionMapping()
    {
        $this->relationnalCollectionMapping = array(
            'selection'      => array(
                'table'      => 'selection',
                'identifier' => 'sku',
                'fields'     => array(
                    'sku' => array(
                        'column'   => 'sku',
                        'length'   => '7',
                        'nullable' => 'false'
                    )
                ),
                'oneToMany'  => array(
                    'fr' => array(
                        'targetEntity' => 'selection_lang',
                        'joinColumn'   => array(
                            'referencedColumnName' => 'id',
                            'name'                 => 'selection_id',
                        ),
                        'references'   => array(
                            'lang_id' => array(
                                'table'                => 'lang',
                                'referencedColumnName' => 'id',
                                'where'                => array(
                                    'iso_code' => 'fr'
                                )
                            )
                        )
                    )
                )
            ),
            'selection_lang' => array(
                'table'      => 'selection_lang',
                'identifier' => 'name',
                'fields'     => array(
                    'name' => array(
                        'column' => 'name'
                    )
                )
            )
        );
    }
}
