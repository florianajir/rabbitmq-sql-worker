<?php

namespace Meup\Bundle\SnotraBundle\ElasticSearch;

interface IndexDictionaryFactoryInterface
{
    /**
     * @return IndexDictionaryInterface
     */
    public function create();
}
