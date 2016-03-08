<?php

namespace Ajir\RabbitMqSqlBundle\DataStructure\Message\Tests;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use JMS\Serializer\SerializerBuilder;
use Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessage;

/**
 *
 */
class SerializerTest extends BaseTestCase
{
    public function testSerialize()
    {
        $serializer = SerializerBuilder::create()->build();
        $message    = new AMQPMessage();

        $json = $serializer->serialize($message, 'json');
        $message2 = $serializer->deserialize($json, 'Ajir\DataStructure\Message\AMQPMessage', 'json');

        $this->assertEquals($message, $message2);
    }
}
