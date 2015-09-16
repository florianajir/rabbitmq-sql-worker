<?php
namespace Meup\Bundle\SnotraBundle\AMPQ;

use JMS\Serializer\Serializer;
use Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionaryInterface;
use Meup\Bundle\SnotraBundle\ElasticSearch\IndexerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

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
     * @param IndexerInterface         $indexer
     * @param Serializer               $serializer
     * @param string                   $msgClass
     * @param string                   $format
     */
    public function __construct(
        IndexDictionaryInterface $indices,
        IndexerInterface $indexer,
        Serializer $serializer,
        $msgClass = self::DEFAULT_MESSAGE_CLASS,
        $format = self::JSON_FORMAT
    ) {
        $this->indices = $indices;
        $this->indexer = $indexer;
        $this->serializer = $serializer;
        $this->msgClass = $msgClass;
        $this->format = $format;

        /* @toDo check if $this->msgClass implements AMPQMessageInterface */
    }

    /**
     * @param AMQPMessage $message
     *
     * @return boolean Execution status (true if everything's of, false if message should be re-queued)
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

        if (strtolower($message->getType()) !== "locale") { //for no search index Locale
            $index = strtolower($message->getIndex());
            if ($this->indices[$index]) {
                $this
                    ->indexer
                    ->execute($this->indices[$index], $message);
            } else {
                //TODO Exception (Unfound ES) + log instead of print_r
                print_r($this->indices);
            }
        }

        return true;
    }
}
