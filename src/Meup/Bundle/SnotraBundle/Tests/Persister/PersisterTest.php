<?php
namespace Meup\Bundle\SnotraBundle\Tests\Persister;

use Meup\Bundle\SnotraBundle\Factory\EntityFactory;
use Meup\Bundle\SnotraBundle\Factory\RelationFactory;
use Meup\Bundle\SnotraBundle\Persister\Persister;
use Meup\Bundle\SnotraBundle\Provider\ProviderInterface;
use PHPUnit_Framework_TestCase;

/**
 * Class DataMapperTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class PersisterTest extends PHPUnit_Framework_TestCase
{

    /**
     * The simple mapping fixture
     *
     * @var array
     */
    protected $data;

    /**
     *
     */
    public function testPersister()
    {
        $provider = $this->getProvider();
        $entityFactory = new EntityFactory('Meup\Bundle\SnotraBundle\Model\Entity');
        $relationFactory = new RelationFactory(
            'Meup\Bundle\SnotraBundle\Model\OneToOneRelation',
            'Meup\Bundle\SnotraBundle\Model\OneToManyRelation',
            'Meup\Bundle\SnotraBundle\Model\ManyToOneRelation',
            'Meup\Bundle\SnotraBundle\Model\ManyToManyRelation'
        );

        $persister = new Persister($provider, $entityFactory, $relationFactory);
        $persister->persist($this->data);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ProviderInterface
     */
    private function getProvider()
    {
        $provider = $this->getMock('Meup\Bundle\SnotraBundle\Provider\ProviderInterface');
        $provider
            ->expects($this->any())
            ->method('getColumnValueWhere')
            ->will($this->returnValue('1'))
        ;
        $provider
            ->expects($this->any())
            ->method('insert')
            ->will($this->returnValue('1'))
        ;
        $provider
            ->expects($this->any())
            ->method('insertOrUpdateIfExists')
            ->will($this->returnValue('1'))
        ;
        $provider
            ->expects($this->atLeastOnce())
            ->method('delete')
            ->will($this->returnValue('1'))
        ;

        return $provider;
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->data = array(
            'user' => array(
                'id' => '1',
                'sku' => 'sku_user',
                'name' => 'toto',
                '_table' => 'user',
                '_related' => array(
                    'manyToOne' => array(
                        'Address' => array(
                            '_relation' => array(
                                'targetEntity' => 'Address',
                                'joinColumn' => array(
                                    'name' => 'address_id',
                                    'referencedColumnName' => 'id',
                                ),
                                'table' => 'address',
                            ),
                            '_data' => array(
                                'Address' => array(
                                    '_table' => 'address',
                                    '_identifier' => 'sku',
                                    'sku' => '1',
                                    'postal_code' => '34000',
                                    'city' => 'Montpellier',
                                ),
                            ),
                        ),
                    ),
                    'oneToOne' => array(
                        'Customer' => array(
                            '_relation' => array(
                                'targetEntity' => 'Customer',
                                'joinColumn' => array(
                                    'name' => 'customer_id',
                                    'referencedColumnName' => 'id',
                                ),
                                'table' => 'customer',
                            ),
                            '_data' => array(
                                'Customer' => array(
                                    '_table' => 'customer',
                                    '_identifier' => 'sku',
                                    'sku' => '1',
                                    'email' => 'foo@bar.com',
                                    'last_purchased' => '2015-06-26T22:22:00+0200',
                                ),
                            ),
                        ),
                    ),
                    'manyToMany' => array(
                        'Group' => array(
                            '_relation' => array(
                                'targetEntity' => 'Group',
                                'joinTable' => array(
                                    'name' => 'users_groups',
                                    'joinColumn' => array(
                                        'name' => 'user_id',
                                        'referencedColumnName' => 'id',
                                    ),
                                    'inverseJoinColumn' => array(
                                        'name' => 'group_id',
                                        'referencedColumnName' => 'id',
                                    ),
                                ),
                                'table' => 'groups',
                            ),
                            '_data' => array(
                                0 => array(
                                    'Group' => array(
                                        '_table' => 'groups',
                                        '_identifier' => 'sku',
                                        'sku' => '0123456789azerty',
                                        'created_at' => '2015-06-01T22:22:00+0200',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'oneToMany' => array(
                        'User' => array(
                            '_relation' => array(
                                'targetEntity' => 'User',
                                'joinColumn' => array(
                                    'name' => 'parent_id',
                                    'referencedColumnName' => 'id',
                                ),
                                'table' => 'user',
                            ),
                            '_data' => array(
                                0 => array(
                                    'User' => array(
                                        '_table' => 'user',
                                        '_identifier' => 'sku',
                                        'id' => '2',
                                        'sku' => 'azertyuiopqsdfgh',
                                        'name' => 'riri',
                                        'created_at' => '2015-06-01T22:22:00+0200',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}
