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
     * @param array $data
     *
     * @return array
     */
    public function transform(array $data);
}
