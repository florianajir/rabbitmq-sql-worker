<?php
namespace Meup\Bundle\SnotraBundle\Factory;

use Meup\Bundle\SnotraBundle\Model\GenericEntityInterface;

/**
 * Interface GenericEntityFactory
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface GenericEntityFactoryInterface
{
    /**
     * @param string $table
     * @param array  $data
     *
     * @return GenericEntityInterface
     */
    public function create($table, array $data);
}
