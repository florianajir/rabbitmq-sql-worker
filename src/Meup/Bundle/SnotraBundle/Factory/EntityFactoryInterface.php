<?php
namespace Meup\Bundle\SnotraBundle\Factory;

use Meup\Bundle\SnotraBundle\Model\EntityInterface;

/**
 * Interface EntityFactory
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface EntityFactoryInterface
{
    /**
     * @param array $data
     *
     * @return EntityInterface
     */
    public function create(array $data);
}
