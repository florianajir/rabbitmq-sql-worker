<?php

namespace Meup\Bundle\SnotraBundle\Tests\ElasticSearch;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Elastica\Index;
use Meup\DataStructure\Message\AMPQMessage;
use Meup\Bundle\SnotraBundle\ElasticSearch\Indexer;
use Meup\Bundle\SnotraBundle\ElasticSearch\DocumentFactory;

/**
 *
 */
class IndexerTest extends BaseTestCase
{
    public function testExecute()
    {
        $response = $this
            ->getMockBuilder('Elastica\Response')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $response
            ->expects($this->once())
            ->method('isOk')
            ->will($this->returnValue(true))
        ;

        $type = $this
            ->getMockBuilder('Elastica\Type')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $type
            ->expects($this->once())
            ->method('addDocument')
            ->will($this->returnValue($response))
        ;

        $index = $this
            ->getMockBuilder('Elastica\Index')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $index
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type))
        ;
        $index
            ->expects($this->once())
            ->method('refresh')
            ->will($this->returnValue(true))
        ;

        $indexer = new Indexer(new DocumentFactory());
        $message = (new AMPQMessage())
            //->setId()
            ->setType(uniqid())
            ->setData(array(uniqid()))
        ;

        $result = $indexer->execute($index, $message);

        $this->assertTrue($result);
    }
}
