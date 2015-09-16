<?php

namespace Meup\Bundle\SnotraBundle\Tests\ElasticSearch;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Meup\Bundle\SnotraBundle\ElasticSearch\DocumentFactory;

/**
 *
 */
class DocumentFactoryTest extends BaseTestCase
{
    /**
     * Test the creation aof a new Document with the factory
     *
     * @return void
     */
    public function testCreate()
    {
        $document = (new DocumentFactory())->create(1, new \stdClass());

        $this->assertInstanceof(DocumentFactory::BASE_CLASS, $document);
    }

    /**
     * Test construction of a factory with an invalid document class
     *
     * @return void
     */
    public function testConstructWithWrongClassname()
    {
        $this->setExpectedException('\InvalidArgumentException');

        new DocumentFactory('\stdClass');
    }
}
