<?php
namespace Meup\Bundle\SnotraBundle\Tests\Model;

use Meup\Bundle\SnotraBundle\Model\Entity;
use PHPUnit_Framework_TestCase;

/**
 * Class EntityTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class EntityTest extends PHPUnit_Framework_TestCase
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
    public function testGetIdentifier()
    {
        $entity = new Entity($this->data);
        $identifier = $entity->getIdentifier();
        $this->assertEquals(array('sku' => 'sku_user'), $identifier);
    }

    /**
     *
     */
    public function testGetProperty()
    {
        $entity = new Entity($this->data);
        $sku = $entity->getProperty('sku');
        $this->assertEquals('sku_user', $sku);
    }

    /**
     *
     */
    public function testGetTable()
    {
        $entity = new Entity($this->data);
        $table = $entity->getTable();
        $this->assertEquals('user', $table);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->data = array(
            '_identifier' => 'sku',
            '_table'      => 'user',
            'id'          => '1',
            'sku'         => 'sku_user',
            'name'        => 'toto',
            '_related'    =>
                array(
                    'manyToOne' =>
                        array(
                            'Address' =>
                                array(
                                    '_relation' => array(
                                        'targetEntity' => 'Address',
                                        'joinColumn'   => array(
                                            'name'                 => 'address_id',
                                            'referencedColumnName' => 'id',
                                        ),
                                        'table'        => 'address',
                                    ),
                                    '_data'     =>
                                        array(
                                            'address' =>
                                                array(
                                                    '_identifier' => 'id',
                                                    'id'          => '1',
                                                    'postal_code' => '34000',
                                                    'city'        => 'Montpellier',
                                                ),
                                        ),
                                ),
                        ),
                ),
        );
    }
}
