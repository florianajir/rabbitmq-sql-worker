<?php

namespace Meup\Bundle\SnotraBundle\Tests\DependencyInjection;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Meup\Bundle\SnotraBundle\DependencyInjection\MeupSnotraExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *
 */
class MeupSnotraExtensionTest extends BaseTestCase
{
    /**
     * @var MeupSnotraExtension
     */
    private $extension;

    /**
     *
     */
    public function testConstruct()
    {
        $ext = new MeupSnotraExtension();

        $this->assertInstanceOf('Meup\Bundle\SnotraBundle\DependencyInjection\MeupSnotraExtension', $ext);
    }

    /**
     * @return MeupSnotraExtension
     */
    protected function getExtension()
    {
        return new MeupSnotraExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
    }

    /**
     *
     */
    public function testLoad()
    {
        $this->extension->load(array(), $this->getContainer());
    }
}
