<?php
namespace Ajir\RabbitMqSqlBundle\DataTransformer;

/**
 * Interface DataTransformerInterface
 *
 * @author Florian Ajir <florianajir@gmail.com>
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
