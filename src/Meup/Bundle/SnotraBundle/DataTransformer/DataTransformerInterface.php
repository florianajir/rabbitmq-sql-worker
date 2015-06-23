<?php
namespace Meup\Bundle\SnotraBundle\DataTransformer;

/**
 * Interface DataTransformerInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface DataTransformerInterface
{
    /**
     * @param array  $data
     * @param string $type
     *
     * @return array
     */
    public function prepare(array $data, $type);
}
