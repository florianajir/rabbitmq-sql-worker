<?php

namespace Meup\Bundle\SnotraEsBundle\ElasticSearch;

/**
 *
 */
interface DocumentFactoryInterface
{
    /**
     * @param mixed $id
     * @param mixed $object
     *
     * @return 
     */
    public function create($id, $object);
}
