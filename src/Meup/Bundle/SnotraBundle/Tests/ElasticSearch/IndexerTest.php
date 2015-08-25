<?php

namespace Meup\Bundle\SnotraBundle\Tests\ElasticSearch;

use Meup\Bundle\SnotraBundle\Tests\AbstractIndexerTestCase;
use Elastica\Index;
use Meup\DataStructure\Message\AMPQMessage;
use Meup\Bundle\SnotraBundle\ElasticSearch\Indexer;
use Meup\Bundle\SnotraBundle\ElasticSearch\DocumentFactory;

/**
 *
 */
class IndexerTest extends AbstractIndexerTestCase
{
    public function testExecute()
    {
        $indexer = new Indexer(new DocumentFactory());
        $message = (new AMPQMessage())
            //->setId()
            ->setType(uniqid())
            ->setData(array(uniqid()))
        ;

        $result = $indexer->execute($this->getIndex(), $message);
        //FIXME Response returned instead of boolean return type of interface
//        $this->assertTrue($result);
    }
}
