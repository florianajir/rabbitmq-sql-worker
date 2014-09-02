<?php

namespace Meup\Bundle\SnotraBundle\AMPQ;

use JMS\Serializer\Serializer;
use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Meup\Bundle\SnotraBundle\ElasticSearch\DocumentFactoryInterface;
use Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionaryInterface;

class ElasticSearchConsumer implements ConsumerInterface
{
    /**
     * @var IndexDictionaryInterface
     */
    private $indexes;

    /**
     * @var DocumentFactoryInterface
     */
    private $documents;

    /**
     * @param DocumentFactoryInterface $documents
     * @param IndexDictionaryInterface $indexes
     * @param Serializer $serializer
     *
     * @return void
     */
    public function __construct(DocumentFactoryInterface $documents, IndexDictionaryInterface $indexes, Serializer $serializer)
    {
        $this->indexes    = $indexes;
        $this->documents  = $documents;
        $this->serializer = $serializer;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return boolean
     */
    public function execute(AMQPMessage $message)
    {
        $object = $this
            ->serializer
            ->deserialize(
                $message->body,
                'stdClass',
                'json'
            )
        ;



        $id = $object->id;
        $result = false;
        foreach ($this->indexes as $index) {
            $response = $index
                ->getType($type)
                ->addDocument(
                    $documents->create($id, $object)
                )
            ;
            $ok = $response->isOk();
            if ($ok) {
                $index->refresh();
            }
            $result |= $ok;
        }
        return $result;
    }
}
