<?php

namespace Ajir\RabbitMqSqlBundle\DataStructure\Message;

interface AMQPMessageFactoryInterface
{
    /**
     * @return AMQPMessageInterface
     */
    public function create();
}
