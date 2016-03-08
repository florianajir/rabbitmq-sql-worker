<?php
namespace Ajir\RabbitMqSqlBundle\Factory;

use Ajir\RabbitMqSqlBundle\Model\EntityInterface;

/**
 * Interface EntityFactory
 *
 * @author Florian Ajir <florianajir@gmail.com>
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
