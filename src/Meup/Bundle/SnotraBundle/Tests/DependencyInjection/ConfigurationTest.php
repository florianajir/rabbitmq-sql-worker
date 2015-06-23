<?php

namespace Meup\Bundle\SnotraBundle\Tests\DependencyInjection;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Meup\Bundle\SnotraBundle\DependencyInjection\Configuration;

/**
 *
 */
class ConfigurationTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testGetConfigTreeBuilder()
    {
        $config = new Configuration();

        $this->assertInstanceOf(
            'Symfony\Component\Config\Definition\Builder\TreeBuilder',
            $config->getConfigTreeBuilder()
        );
    }
}
