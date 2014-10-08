<?php

namespace Meup\Bundle\SnotraBundle\AMPQ;

use JMS\Serializer\Serializer;
use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Meup\Bundle\SnotraBundle\ElasticSearch\IndexerInterface;
use Meup\Bundle\SnotraBundle\ElasticSearch\DocumentFactoryInterface;
use Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionaryInterface;

/**
 *
 */
class ElasticSearchConsumer implements ConsumerInterface
{
    const DEFAULT_MESSAGE_CLASS = 'Meup\DataStructure\Message\AMPQMessage';
    const JSON_FORMAT = 'json';
    const XML_FORMAT  = 'xml';

    /**
     * @var IndexDictionaryInterface
     */
    private $indices;

    /**
     * @var IndexerInterface
     */
    private $indexer;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param IndexDictionaryInterface $indices
     * @param IndexerInterface $indexer
     * @param Serializer $serializer
     *
     * @return void
     */
    public function __construct(
        IndexDictionaryInterface $indices,
        IndexerInterface $indexer,
        Serializer $serializer,
        $msgClass = self::DEFAULT_MESSAGE_CLASS,
        $format = self::JSON_FORMAT
    ) {
        $this->indices    = $indices;
        $this->indexer    = $indexer;
        $this->serializer = $serializer;
        $this->msgClass   = $msgClass;
        $this->format     = $format;

        /* @toDo check if $this->msgClass implements AMPQMessageInterface */
    }

    /**
     * @param AMQPMessage $message
     *
     * @return void
     */
    public function execute(AMQPMessage $message)
    {
        /* deserialize the message body */
        $message = $this
            ->serializer
            ->deserialize(
                $message->body,
                $this->msgClass,
                $this->format
            )
        ;

        /* index the object in each defined ElasticSearch indinces */
        foreach ($this->indices as $index) {
            $this
                ->indexer
                ->execute($index, $message)
            ;
        }
    }
}
