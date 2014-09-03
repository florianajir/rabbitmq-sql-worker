<?php

namespace Meup\Bundle\SnotraBundle\Tests\AMPQ;

use Meup\Bundle\SnotraBundle\Tests\AbstractIndexerTestCase;
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
class ElasticSearchConsumerTest extends AbstractIndexerTestCase
{
    /**
     * @return void
     */
    public function testExecute()
    {
        $serializer = SerializerBuilder::create()->build();
        $indices    = new IndexDictionary('Elastica\Index');
        $indices['my_index'] = $this->getIndex();
        $docFactory = new DocumentFactory();
        $indexer    = new Indexer($docFactory);
        $consumer   = new ElasticSearchConsumer($indices, $indexer, $serializer);
        $msg        = new AMQPMessage();
        $msg->body  = $serializer
            ->serialize(
                (new GnaaMessage())
                    ->setId(uniqid())
                    ->setType(uniqid())
                    ->setData($serializer->serialize(array(), 'json')),
                'json'
            )
        ;

        $result = $consumer->execute($msg);

        $this->assertTrue($result);
    }
}
