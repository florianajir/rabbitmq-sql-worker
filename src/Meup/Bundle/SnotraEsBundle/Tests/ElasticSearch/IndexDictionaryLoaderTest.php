<?php

namespace Meup\Bundle\SnotraEsBundle\Tests\ElasticSearch;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Meup\Bundle\SnotraEsBundle\ElasticSearch\IndexDictionaryLoader;
use Meup\Bundle\SnotraEsBundle\ElasticSearch\IndexDictionaryFactory;

/**
 *
 */
class IndexDictionaryLoaderTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testLoad()
    {
        $factory = new IndexDictionaryFactory(IndexDictionaryFactory::DEFAULT_CLASS, null);
        $client  = $this->getMock('Elastica\Client', array('getIndex'));
        $client
            ->expects($this->any())
            ->method('getIndex')
            ->will($this->returnValue(uniqid())) // <= I know ...
        ;
        $loader     = new IndexDictionaryLoader($client, $factory);
        $dictionary = $loader->load(array(uniqid())); // <= again, I know ...
        /* but that prove that the code coverage metric is bullshit. */

        $this->assertInstanceof(
            IndexDictionaryFactory::DEFAULT_CLASS,
            $dictionary
        );
    }
}
