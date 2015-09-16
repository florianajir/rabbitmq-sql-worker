<?php
namespace Meup\Bundle\SnotraBundle\Factory;

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
     * @return RelationInterface
     */
    public function create($relation, $data);
}
