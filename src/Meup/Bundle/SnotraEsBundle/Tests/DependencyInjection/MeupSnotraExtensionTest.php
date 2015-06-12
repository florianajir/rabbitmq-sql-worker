<?php

namespace Meup\Bundle\SnotraEsBundle\Tests\DependencyInjection;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Meup\Bundle\SnotraEsBundle\DependencyInjection\MeupSnotraExtension;

/**
 *
 */
class MeupSnotraExtensionTest extends BaseTestCase
{
    public function testConstruct()
    {
        $ext = new MeupSnotraExtension();

        $this->assertInstanceOf('Meup\Bundle\SnotraEsBundle\DependencyInjection\MeupSnotraExtension', $ext);
    }
}
