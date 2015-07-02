<?php
namespace Meup\Bundle\SnotraBundle\Manager;

/**
 * Interface ManagerInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface ManagerInterface
{
    /**
     * Persist data
     *
     * @param array $data An associative array containing column-value pairs.
     */
    public function persist(array $data);
}
