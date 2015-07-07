<?php
namespace Meup\Bundle\SnotraBundle\Persister;

/**
 * Interface PersisterInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface PersisterInterface
{
    /**
     * Persist data
     *
     * @param array $data An associative array containing column-value pairs.
     */
    public function persist(array $data);
}
