<?php

namespace Meup\Bundle\SnotraEsBundle\Tests\ElasticSearch;

use Meup\Bundle\SnotraEsBundle\Tests\AbstractIndexerTestCase;
use Elastica\Index;
use Meup\DataStructure\Message\AMPQMessage;
use Meup\Bundle\SnotraEsBundle\ElasticSearch\Indexer;
use Meup\Bundle\SnotraEsBundle\ElasticSearch\DocumentFactory;

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

        $this->assertTrue($result);
    }
}
