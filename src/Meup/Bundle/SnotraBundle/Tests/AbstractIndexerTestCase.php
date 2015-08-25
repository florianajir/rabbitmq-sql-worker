<?php

namespace Meup\Bundle\SnotraBundle\Tests;

use \PHPUnit_Framework_TestCase as BaseTestCase;

/**
 *
 */
abstract class AbstractIndexerTestCase extends BaseTestCase
{
    /**
     * @return \Elastica\Index
     */
    protected function getIndex()
    {
        $response = $this
            ->getMockBuilder('Elastica\Response')
            ->disableOriginalConstructor()
            ->getMock()
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

        return $index;
    }
}
