<?php

namespace Meup\Bundle\SnotraEsBundle\ElasticSearch;

interface IndexDictionaryFactoryInterface
{
    /**
     * @return IndexDictionaryInterface
     */
    public function create();
}
