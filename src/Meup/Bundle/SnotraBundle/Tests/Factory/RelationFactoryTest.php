<?php
/**
 * This file is part of the Meup Snotra.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/snotra>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\SnotraBundle\Tests\Factory;

use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\Factory\RelationFactory;

/**
 *
 */
class RelationFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $classImplement = 'Meup\Bundle\SnotraBundle\Model\RelationInterface';
    protected $oneToOneRelationClass = 'Meup\Bundle\SnotraBundle\Model\OneToOneRelation';
    protected $manyToOneRelationClass = 'Meup\Bundle\SnotraBundle\Model\ManyToOneRelation';
    protected $oneToManyRelationClass = 'Meup\Bundle\SnotraBundle\Model\OneToManyRelation';
    protected $manyToManyRelationClass = 'Meup\Bundle\SnotraBundle\Model\ManyToManyRelation';

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

        $this->assertInstanceOf('Meup\Bundle\SnotraBundle\Factory\RelationFactoryInterface', $factory);
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
                        'table'            => 'selection_lang',
                        'targetEntity'     => 'selection_lang',
                        'joinColumn'       => array(
                            'referencedColumnName' => 'id',
                            'name'                 => 'selection_id',
                        ),
                        'removeReferenced' => true,
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
    public function testCreateAssumingException()
    {
        $this->setExpectedException('InvalidArgumentException');
        new RelationFactory('stdClass', 'stdClass', 'stdClass', 'stdClass');
    }
}
