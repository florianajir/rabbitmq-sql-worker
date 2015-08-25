<?php
namespace Meup\Bundle\SnotraBundle\Tests\AMPQ;

use JMS\Serializer\SerializerBuilder;
use Meup\Bundle\SnotraBundle\AMPQ\SqlConsumer;
use Meup\DataStructure\Message\AMPQMessage as GnaaMessage;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class SqlConsumerTest
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SqlConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testExecute()
    {
        $transformer = $this->getMock('Meup\Bundle\SnotraBundle\DataTransformer\DataTransformerInterface');
        $transformer
            ->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue(array()));
        $persister = $this->getMock('Meup\Bundle\SnotraBundle\Persister\PersisterInterface');
        $serializer = SerializerBuilder::create()->build();
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $consumer = new SqlConsumer($transformer, $persister, $serializer, $logger);
        $msg = new AMQPMessage();
        $msg->body = $serializer
            ->serialize(
                (new GnaaMessage())
                    ->setType(uniqid())
                    ->setData($serializer->serialize(array(), 'json')),
                'json'
            );
        $result = $consumer->execute($msg);
        $this->assertTrue($result);
    }
}
