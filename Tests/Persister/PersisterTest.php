<?php
namespace Ajir\RabbitMqSqlBundle\Tests\Persister;

use Ajir\RabbitMqSqlBundle\Factory\EntityFactory;
use Ajir\RabbitMqSqlBundle\Factory\RelationFactory;
use Ajir\RabbitMqSqlBundle\Persister\Persister;
use Ajir\RabbitMqSqlBundle\Provider\ProviderInterface;
use PHPUnit_Framework_TestCase;

/**
 * Class DataMapperTest
 *
 * @author Florian Ajir <florianajir@gmail.com>
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
        $entityFactory = new EntityFactory('Ajir\RabbitMqSqlBundle\Model\Entity');
        $relationFactory = new RelationFactory(
            'Ajir\RabbitMqSqlBundle\Model\OneToOneRelation',
            'Ajir\RabbitMqSqlBundle\Model\OneToManyRelation',
            'Ajir\RabbitMqSqlBundle\Model\ManyToOneRelation',
            'Ajir\RabbitMqSqlBundle\Model\ManyToManyRelation'
        );

        $persister = new Persister($provider, $entityFactory, $relationFactory);
        $persister->persist($this->data);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ProviderInterface
     */
    private function getProvider()
    {
        $provider = $this->getMock('Ajir\RabbitMqSqlBundle\Provider\ProviderInterface');
        $provider
            ->expects($this->any())
            ->method('getColumnValueWhere')
            ->will($this->returnValue('1'));
        $provider
            ->expects($this->any())
            ->method('insert')
            ->will($this->returnValue('1'));
        $provider
            ->expects($this->any())
            ->method('insertOrUpdateIfExists')
            ->will($this->returnValue('1'));
        $provider
            ->expects($this->atLeastOnce())
            ->method('delete')
            ->will($this->returnValue('1'));

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
                '_table' => 'user'
            )
        );
        $this->setUpOneToOneRelation();
        $this->setUpOneToManyRelation();
        $this->setUpManyToOneRelation();
        $this->setUpManyToManyRelation();
    }

    /**
     * Set up oneToOne relation fixtures
     */
    private function setUpOneToOneRelation()
    {
        $this->data['user']['_related']['oneToOne'] = array(
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
            )
        );
    }

    /**
     * Set up oneToMany relation fixtures
     */
    private function setUpOneToManyRelation()
    {
        $this->data['user']['_related']['oneToMany'] = array(
            'User' => array(
                '_relation' => array(
                    'targetEntity' => 'User',
                    'joinColumn' => array(
                        'name' => 'parent_id',
                        'referencedColumnName' => 'id'
                    ),
                    'table' => 'user'
                ),
                '_data' => array(
                    0 => array(
                        'User' => array(
                            '_table' => 'user',
                            '_identifier' => 'sku',
                            'id' => '2',
                            'sku' => 'azertyuiopqsdfgh',
                            'name' => 'riri',
                            'created_at' => '2015-06-01T22:22:00+0200'
                        )
                    )
                )
            )
        );
    }

    /**
     * Set up manyToOne relation fixtures
     */
    private function setUpManyToOneRelation()
    {
        $this->data['user']['_related']['manyToOne'] = array(
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
            )
        );
    }

    /**
     * Set up manyToMany relation fixtures
     */
    private function setUpManyToManyRelation()
    {
        $this->data['user']['_related']['manyToMany'] = array(
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
                )
            )
        );
    }
}
