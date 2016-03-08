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
     * Prepare data from RabbitMQMessage to fit with mapping configuration.
     *
     * @param string $type
     * @param array  $data
     *
     * @return array
     */
    public function prepare($type, array $data);
}
