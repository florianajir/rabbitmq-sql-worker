<?php

namespace Ajir\RabbitMqSqlBundle\Tests\DataStructure\Message;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessage;

/**
 *
 */
class AMQPMessageTest extends BaseTestCase
{
    /**
     * Test the $id attribute's accessors
     *
     * @return void
     */
    public function testId()
    {
        $id = rand(1, 999);
        $message = new AMQPMessage();
        $message->setId($id);

        $this->assertEquals($id, $message->getId());
    }

    /**
     * Test the $index attribute's accessors
     *
     * @return void
     */
    public function testIndex()
    {
        $index = uniqid();
        $message = new AMQPMessage();
        $message->setIndex($index);

        $this->assertEquals($index, $message->getIndex());
    }

    /**
     * Test the $type attribute's accessors
     *
     * @return void
     */
    public function testType()
    {
        $type = uniqid();
        $message = new AMQPMessage();
        $message->setType($type);

        $this->assertEquals($type, $message->getType());
    }

    /**
     * Test the $data attribute's accessors
     *
     * @return void
     */
    public function testData()
    {
        $data = uniqid();
        $message = new AMQPMessage();
        $message->setData($data);

        $this->assertEquals($data, $message->getData());
    }
}
