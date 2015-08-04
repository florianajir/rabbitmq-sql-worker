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
     * @param string $table
     * @param array  $data
     *
     * @return EntityInterface
     */
    public function create($table, array $data);
}
