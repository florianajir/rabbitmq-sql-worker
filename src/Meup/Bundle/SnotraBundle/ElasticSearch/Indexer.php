<?php

namespace Meup\Bundle\SnotraBundle\ElasticSearch;

use Elastica\Index;
use Meup\DataStructure\Message\AMPQMessageInterface;

/**
 *
 */
class Indexer implements IndexerInterface
{
    /**
     * @var boolean
     */
    private $autoRefresh;

    /**
     * @var DocumentFactoryInterface
     */
    private $documentFactory;

    /**
     * @param DocumentFactoryInterface $documentFactory
     * @param boolean $autoRefresh
     */
    public function __construct(DocumentFactoryInterface $documentFactory, $autoRefresh = true)
    {
        $this->documentFactory = $documentFactory;
        $this->autoRefresh     = $autoRefresh;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Index $index, AMPQMessageInterface $message)
    {
        $newDoc = $this
            ->documentFactory
            ->create(
                $message->getId(),
                $message->getData()
            );
        $response = $index
            ->getType($message->getType())
            ->addDocument(
                $newDoc
            )
        ;
        //$index->refresh();

        return $response;
    }
}
