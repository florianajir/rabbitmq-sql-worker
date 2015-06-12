<?php

namespace Meup\Bundle\SnotraEsBundle\ElasticSearch;

use Elastica\Index;
use Elastica\Exception\NotFoundException;
use Meup\DataStructure\Message\AMPQMessageInterface;
use Meup\Bundle\SnotraEsBundle\ElasticSearch\DocumentFactoryInterface;

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
     *
     * @return void
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
