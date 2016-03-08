<?php
namespace Ajir\RabbitMqSqlBundle\Tests\AMQP;

use Exception;
use InvalidArgumentException;
use JMS\Serializer\SerializerBuilder;
use Ajir\RabbitMqSqlBundle\AMQP\SqlConsumer;
use Ajir\RabbitMqSqlBundle\DataTransformer\DataTransformerInterface;
use Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessage as GnaaMessage;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class SqlConsumerTest
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
class SqlConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testExecuteWithoutError()
    {
        $transformer = $this->getTransformerMock();
        $persister = $this->getPersisterMock();
        $serializer = $this->getSerializer();
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
        ;
        $consumer = new SqlConsumer($transformer, $persister, $serializer, $logger);
        $data = new GnaaMessage();
        $data
            ->setType(uniqid())
            ->setData($serializer->serialize(array(), 'json'));
        $msg = new AMQPMessage();
        $msg->body = $serializer->serialize($data, 'json');
        $result = $consumer->execute($msg);
        $this->assertTrue($result);
    }

    /**
     * @param int $nbCallsPrepare
     * @param \PHPUnit_Framework_MockObject_Stub $returned
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|DataTransformerInterface
     */
    private function getTransformerMock($nbCallsPrepare = 1, $returned = null)
    {
        if (is_null($returned)) {
            $returned = $this->returnValue(array());
        }
        $transformer = $this->getMock('Ajir\RabbitMqSqlBundle\DataTransformer\DataTransformerInterface');
        $transformer
            ->expects($this->exactly($nbCallsPrepare))
            ->method('prepare')
            ->will($returned);

        return $transformer;
    }

    /**
     * @return \Ajir\RabbitMqSqlBundle\Persister\PersisterInterface
     */
    private function getPersisterMock()
    {
        $persister = $this->getMock('Ajir\RabbitMqSqlBundle\Persister\PersisterInterface');

        return $persister;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('info', 'warning', 'error'))
            ->getMock();

        return $logger;
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    private function getSerializer()
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer;
    }

    /**
     *
     */
    public function testExecuteWithInvalidArgumentThrew()
    {
        $transformer = $this->getTransformerMock(
            1,
            $this->throwException(new InvalidArgumentException)
        );
        $persister = $this->getPersisterMock();
        $serializer = $this->getSerializer();
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
        ;
        $logger
            ->expects($this->once())
            ->method('warning')
        ;
        $consumer = new SqlConsumer($transformer, $persister, $serializer, $logger);
        $data = new GnaaMessage();
        $data
            ->setType(uniqid())
            ->setData($serializer->serialize(array(), 'json'));
        $msg = new AMQPMessage();
        $msg->body = $serializer->serialize($data, 'json');
        $result = $consumer->execute($msg);
        $this->assertTrue($result);
    }

    /**
     *
     */
    public function testExecuteWithExceptionThrew()
    {
        $transformer = $this->getTransformerMock(
            1,
            $this->throwException(new Exception())
        );
        $persister = $this->getPersisterMock();
        $serializer = $this->getSerializer();
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
        ;
        $logger
            ->expects($this->once())
            ->method('error')
        ;
        $consumer = new SqlConsumer($transformer, $persister, $serializer, $logger);
        $data = new GnaaMessage();
        $data
            ->setType(uniqid())
            ->setData($serializer->serialize(array(), 'json'));
        $msg = new AMQPMessage();
        $msg->body = $serializer->serialize($data, 'json');
        $this->setExpectedException('Exception');
        $consumer->execute($msg);
    }



    /**
     *
     */
    public function testExecuteWithIgnoredType()
    {
        $type = 'galenical';
        $transformer = $this->getTransformerMock(0);
        $persister = $this->getPersisterMock();
        $serializer = $this->getSerializer();
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
        ;
        $consumer = new SqlConsumer($transformer, $persister, $serializer, $logger, array($type));
        $data = new GnaaMessage();
        $data
            ->setType($type)
            ->setData($serializer->serialize(array(), 'json'));
        $msg = new AMQPMessage();
        $msg->body = $serializer->serialize($data, 'json');
        $result = $consumer->execute($msg);
        $this->assertTrue($result);
    }
}
