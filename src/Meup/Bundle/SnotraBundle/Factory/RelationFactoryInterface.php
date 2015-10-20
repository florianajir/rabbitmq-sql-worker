<?php
namespace Meup\Bundle\SnotraBundle\Factory;

use Meup\Bundle\SnotraBundle\Model\ManyToManyRelation;
use Meup\Bundle\SnotraBundle\Model\ManyToOneRelation;
use Meup\Bundle\SnotraBundle\Model\OneToManyRelation;
use Meup\Bundle\SnotraBundle\Model\OneToOneRelation;
use Meup\Bundle\SnotraBundle\Model\RelationInterface;

/**
 * Interface RelationFactoryInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface RelationFactoryInterface
{
    /**
     * @param string $relation
     * @param array $data
     *
     * @return RelationInterface|ManyToManyRelation|ManyToOneRelation|OneToManyRelation|OneToOneRelation
     */
    public function create($relation, $data);
}
