<?php
namespace Meup\Bundle\SnotraBundle\Provider;

/**
 * Interface SqlProviderInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface SqlProviderInterface
{
    /**
     * @param string $table
     * @param array  $data
     */
    public function insert($table, array $data);

    /**
     * @param string $table
     * @param array  $data
     * @param array  $identifier
     */
    public function update($table, array $data, array $identifier);
}