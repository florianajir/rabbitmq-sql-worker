<?php

namespace Ajir\RabbitMqSqlBundle\Tests\DataStructure\Message;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use JMS\Serializer\SerializerBuilder;
use Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessage;

/**
 *
 */
class SerializerTest extends BaseTestCase
{
    /**
     * Load JMS Annotations
     */
    public function setUp()
    {
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation', __DIR__.'/vendor/jms/serializer/src'
        );
    }

    public function testSerialize()
    {
        $serializer = SerializerBuilder::create()->build();
        $message    = new AMQPMessage();

        $json = $serializer->serialize($message, 'json');
        $message2 = $serializer->deserialize($json, 'Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessage', 'json');

        $this->assertEquals($message, $message2);
    }
}
