<?php

namespace Ajir\RabbitMqSqlBundle\Tests\Factory;

use Ajir\RabbitMqSqlBundle\DataMapper\DataMapper;
use Ajir\RabbitMqSqlBundle\Factory\RelationFactory;

/**
 * @author Florian Ajir <florianajir@gmail.com>
 */
class RelationFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $classImplement = 'Ajir\RabbitMqSqlBundle\Model\RelationInterface';
    protected $oneToOneRelationClass = 'Ajir\RabbitMqSqlBundle\Model\OneToOneRelation';
    protected $manyToOneRelationClass = 'Ajir\RabbitMqSqlBundle\Model\ManyToOneRelation';
    protected $oneToManyRelationClass = 'Ajir\RabbitMqSqlBundle\Model\OneToManyRelation';
    protected $manyToManyRelationClass = 'Ajir\RabbitMqSqlBundle\Model\ManyToManyRelation';

    /**
     *
     */
    public function testFactoryInterface()
    {
        $factory = new RelationFactory(
            $this->oneToOneRelationClass,
            $this->manyToOneRelationClass,
            $this->oneToManyRelationClass,
            $this->manyToManyRelationClass
        );

        $this->assertInstanceOf('Ajir\RabbitMqSqlBundle\Factory\RelationFactoryInterface', $factory);
    }

    /**
     *
     */
    public function testCreateOneToOne()
    {
        $factory = new RelationFactory(
            $this->oneToOneRelationClass,
            $this->manyToOneRelationClass,
            $this->oneToManyRelationClass,
            $this->manyToManyRelationClass
        );
        $this->assertInstanceOf(
            $this->classImplement,
            $factory->create(
                DataMapper::RELATION_ONE_TO_ONE,
                array(
                    '_relation' => array(
                        'table'        => 'test',
                        'targetEntity' => 'test',
                        'joinColumn'   => array(
                            'name'                 => 'test',
                            'referencedColumnName' => 'id'
                        ),
                    ),
                    '_data'     => array(
                        'test' => array()
                    )
                )
            )
        );
    }

    /**
     *
     */
    public function testCreateManyToOne()
    {
        $factory = new RelationFactory(
            $this->oneToOneRelationClass,
            $this->manyToOneRelationClass,
            $this->oneToManyRelationClass,
            $this->manyToManyRelationClass
        );

        $this->assertInstanceOf(
            $this->classImplement,
            $factory->create(
                DataMapper::RELATION_MANY_TO_ONE,
                array(
                    '_relation' => array(
                        'table'        => 'test',
                        'targetEntity' => 'test',
                        'joinColumn'   => array(
                            'name'                 => 'test',
                            'referencedColumnName' => 'id'
                        ),
                    ),
                    '_data'     => array(
                        'test' => array()
                    )
                )
            )
        );
    }

    /**
     *
     */
    public function testCreateOneToMany()
    {
        $factory = new RelationFactory(
            $this->oneToOneRelationClass,
            $this->manyToOneRelationClass,
            $this->oneToManyRelationClass,
            $this->manyToManyRelationClass
        );

        $this->assertInstanceOf(
            $this->classImplement,
            $factory->create(
                DataMapper::RELATION_ONE_TO_MANY,
                array(
                    '_relation' => array(
                        'removeReferenced' => 'true',
                        'table'            => 'selection_lang',
                        'targetEntity'     => 'selection_lang',
                        'joinColumn'       => array(
                            'referencedColumnName' => 'id',
                            'name'                 => 'selection_id',
                        ),
                        'references'       => array(
                            'lang_id' => array(
                                'table'                => 'lang',
                                'referencedColumnName' => 'id',
                                'where'                => array(
                                    'iso_code' => 'fr'
                                )
                            ),
                        ),
                    ),
                    '_data'     => array(
                        'selection_lang' => array()
                    )
                )
            )
        );
    }

    /**
     *
     */
    public function testCreateManyToMany()
    {
        $factory = new RelationFactory(
            $this->oneToOneRelationClass,
            $this->manyToOneRelationClass,
            $this->oneToManyRelationClass,
            $this->manyToManyRelationClass
        );

        $this->assertInstanceOf(
            $this->classImplement,
            $factory->create(
                DataMapper::RELATION_MANY_TO_MANY,
                array(
                    '_relation' => array(
                        'targetEntity' => 'Category',
                        'table'        => 'category',
                        'joinTable'    => array(
                            'name'              => 'product_category',
                            'joinColumn'        => array(
                                'name'                 => 'product_id',
                                'referencedColumnName' => 'id'
                            ),
                            'inverseJoinColumn' => array(
                                'name'                 => 'category_id',
                                'referencedColumnName' => 'id'
                            )
                        ),
                    ),
                    '_data'     => array()
                )
            )
        );
    }

    /**
     *
     */
    public function testConstructAssumingException()
    {
        $this->setExpectedException('InvalidArgumentException');
        new RelationFactory('stdClass', 'stdClass', 'stdClass', 'stdClass');
    }

    /**
     *
     */
    public function testCreateInvalidRelationParameter()
    {
        $this->setExpectedException('InvalidArgumentException');
        $factory = new RelationFactory(
            $this->oneToOneRelationClass,
            $this->manyToOneRelationClass,
            $this->oneToManyRelationClass,
            $this->manyToManyRelationClass
        );
        $factory->create('invalid', array());
    }
}
