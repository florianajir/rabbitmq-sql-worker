<?php
namespace Meup\Bundle\SnotraBundle\Tests\Model;

use Meup\Bundle\SnotraBundle\Model\OneToManyRelation;
use PHPUnit_Framework_TestCase;

/**
 * Class OneToManyRelationTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class OneToManyRelationTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testInstanciate()
    {
        $data = array(
            '_relation' => array(
                'targetEntity' => 'selection_lang',
                'joinColumn'   => array(
                    'referencedColumnName' => 'id',
                    'name'                 => 'selection_id',
                ),
                'removeReferenced' => 'true',
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
        );
        $relation = new OneToManyRelation($data);
        $this->assertTrue($relation->isRemoveReferenced());
        $this->assertEquals($data['_data'], $relation->getEntities());
        $this->assertEquals($data['_relation']['references'], $relation->getReferences());
        $this->assertEquals($data['_relation']['targetEntity'], $relation->getEntityName());
        $this->assertEquals($data['_relation']['joinColumn']['name'], $relation->getJoinColumnName());
        $this->assertEquals(
            $data['_relation']['joinColumn']['referencedColumnName'],
            $relation->getJoinColumnReferencedColumnName()
        );
        $this->assertEquals($data['_relation']['table'], $relation->getTable());
    }
    /**
     *
     */
    public function testNotRemoveReferenced()
    {
        $data = array(
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
        );
        $relation = new OneToManyRelation($data);
        $this->assertFalse($relation->isRemoveReferenced());
    }
}
