<?php

namespace Ajir\RabbitMqSqlBundle\Tests\DataStructure\Message;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessageFactory;

/**
 *
 */
class AMQPMessageFactoryTest extends BaseTestCase
{
    /**
     * Test creating a new AMQPMessage instance with the factory
     *
     * @return void
     */
    public function testCreate()
    {
        $factory = new AMQPMessageFactory();

        $this->assertInstanceof(
            AMQPMessageFactory::DEFAULT_CLASS,
            $factory->create()
        );
    }

    /**
     * Test using the factory with a wrong AMQPMessage class
     * 
     * @return void
     */
    public function testCreateWithWrongClass()
    {
        $this->setExpectedException('InvalidArgumentException');

        $factory = new AMQPMessageFactory('stdClass');
    }
}
