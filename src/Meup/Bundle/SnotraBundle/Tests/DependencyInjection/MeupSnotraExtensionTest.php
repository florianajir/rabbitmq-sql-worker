<?php

namespace Meup\Bundle\SnotraBundle\Tests\DependencyInjection;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Meup\Bundle\SnotraBundle\DependencyInjection\MeupSnotraExtension;

/**
 *
 */
class MeupSnotraExtensionTest extends BaseTestCase
{
    public function testConstruct()
    {
        $ext = new MeupSnotraExtension();

        $this->assertInstanceOf('Meup\Bundle\SnotraBundle\DependencyInjection\MeupSnotraExtension', $ext);
    }
}
