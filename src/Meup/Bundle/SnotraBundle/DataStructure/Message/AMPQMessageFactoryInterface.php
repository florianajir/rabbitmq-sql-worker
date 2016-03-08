<?php

namespace Meup\DataStructure\Message;

interface AMQPMessageFactoryInterface
{
    /**
     * @return AMQPMessageInterface
     */
    public function create();
}
