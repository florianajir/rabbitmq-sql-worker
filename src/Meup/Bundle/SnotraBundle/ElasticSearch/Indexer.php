<?php

namespace Meup\Bundle\SnotraBundle\ElasticSearch;

use Elastica\Index;
use Meup\DataStructure\Message\AMPQMessageInterface;
use Meup\Bundle\SnotraBundle\ElasticSearch\DocumentFactoryInterface;

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
        $response = $index
            ->getType($message->getType())
            ->addDocument(
                $this
                    ->documentFactory
                    ->create(
                        $message->getId(),
                        $message->getData()
                    )
            )
        ;
        $ok = $response->isOk();
        if ($ok) {
            $index->refresh();
        }
        return $ok;
    }
}
