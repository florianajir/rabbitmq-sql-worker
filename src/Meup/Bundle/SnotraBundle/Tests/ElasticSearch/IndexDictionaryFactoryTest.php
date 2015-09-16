<?php

namespace Meup\Bundle\SnotraBundle\Tests\ElasticSearch;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionaryFactory;

/**
 *
 */
class IndexDictionaryFactoryTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testConstructWithAnInvalidClass()
    {
        $this->setExpectedException('\InvalidArgumentException');

        new IndexDictionaryFactory('\stdClass', '\stdClass');
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $factory = new IndexDictionaryFactory('\stdClass', IndexDictionaryFactory::DEFAULT_CLASS);

        $this->assertInstanceOf(IndexDictionaryFactory::DEFAULT_CLASS, $factory->create());
    }
}
