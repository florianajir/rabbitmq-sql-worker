<?php

namespace Meup\Bundle\SnotraBundle\ElasticSearch;

use Meup\DataStructure\Dictionary;

/**
 *
 */
class IndexDictionary extends Dictionary implements IndexDictionaryInterface
{
    /**
     * @param string $indexClassName
     *
     * @return void
     */
    public function __construct($indexClassName)
    {
        parent::__construct(null, $indexClassName);
    }
}
