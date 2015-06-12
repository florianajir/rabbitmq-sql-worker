<?php

namespace Meup\Bundle\SnotraEsBundle\Tests;

use \PHPUnit_Framework_TestCase as BaseTestCase;

/**
 *
 */
abstract class AbstractIndexerTestCase extends BaseTestCase
{
    /**
     * @return Elastica\Index
     */
    protected function getIndex()
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

        return $index;
    }
}
