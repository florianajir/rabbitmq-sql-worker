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
        $serializer = SerializerBuilder::create()->build();
        $provider = $this
            ->getMockBuilder('Meup\Bundle\SnotraBundle\Provider\SqlProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $transformer = $this
            ->getMockBuilder('Meup\Bundle\SnotraBundle\DataTransformer\SqlDataTransformer')
            ->disableOriginalConstructor()
            ->getMock();
        $transformer
            ->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue(array()));
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $consumer = new SqlConsumer($provider, $transformer, $serializer, $logger);
        $msg = new AMQPMessage();
        $msg->body = $serializer
            ->serialize(
                (new GnaaMessage())
                    ->setId(uniqid())
                    ->setType(uniqid())
                    ->setData($serializer->serialize(array(), 'json')),
                'json'
            );

        $result = $consumer->execute($msg);

        $this->assertTrue($result);
    }
}
