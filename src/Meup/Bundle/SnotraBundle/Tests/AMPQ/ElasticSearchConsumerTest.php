<?php

namespace Meup\Bundle\SnotraBundle\Tests\AMPQ;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use JMS\Serializer\SerializerBuilder;
use PhpAmqpLib\Message\AMQPMessage;
use Meup\DataStructure\Message\AMPQMessage as GnaaMessage;
use Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionary;
use Meup\Bundle\SnotraBundle\ElasticSearch\Indexer;
use Meup\Bundle\SnotraBundle\ElasticSearch\DocumentFactory;
use Meup\Bundle\SnotraBundle\AMPQ\ElasticSearchConsumer;

/**
 *
 */
class ElasticSearchConsumerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testExecute()
    {
        $response = $this
            ->getMockBuilder('Elastica\Response')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $response
            ->expects($this->once())
            ->method('isOk')
            ->will($this->returnValue(true))
        ;

        $type = $this
            ->getMockBuilder('Elastica\Type')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $type
            ->expects($this->once())
            ->method('addDocument')
            ->will($this->returnValue($response))
        ;

        $index = $this
            ->getMockBuilder('Elastica\Index')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $index
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type))
        ;
        $index
            ->expects($this->once())
            ->method('refresh')
            ->will($this->returnValue(true))
        ;

        $serializer = SerializerBuilder::create()->build();
        $indices    = new IndexDictionary('Elastica\Index');
        $indices['my_index'] = $index;
        $docFactory = new DocumentFactory();
        $indexer    = new Indexer($docFactory);
        $consumer   = new ElasticSearchConsumer($indices, $indexer, $serializer);
        $msg        = new AMQPMessage();
        $msg->body  = $serializer
            ->serialize(
                (new GnaaMessage())
                    ->setId(uniqid())
                    ->setType(uniqid())
                    ->setData($serializer->serialize(array(), 'json'))
                ,
                'json'
            )
        ;

        $result = $consumer->execute($msg);

        $this->assertTrue($result);
    }
}
