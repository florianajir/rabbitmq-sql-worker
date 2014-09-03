<?php

namespace Meup\Bundle\SnotraBundle\ElasticSearch;

use Elastica\Client as ElasticaClient;

/**
 *
 */
class IndexDictionaryLoader
{
    /**
     * @var ElasticaClient
     */
    private $elasticaClient;

    /**
     * IndexDictionaryFactory
     */
    private $indexDictionaryFactory;

    /**
     * @param ElasticaClient $elasticaClient
     * @param IndexDictionaryFactory $indexDictionaryFactory
     *
     * @return void
     */
    public function __construct(ElasticaClient $elasticaClient, IndexDictionaryFactory $indexDictionaryFactory)
    {
        $this->elasticaClient         = $elasticaClient;
        $this->indexDictionaryFactory = $indexDictionaryFactory;
    }

    /**
     * @param Array $names
     *
     * @return IndexDictionaryInterface
     */
    public function load(Array $names)
    {
        $dictionary = $this->indexDictionaryFactory->create();
        foreach ($names as $name) {
            $dictionary[$name] = $this->elasticaClient->getIndex($name);
        }
        return $dictionary;
    }
}
