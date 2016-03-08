<?php

namespace Ajir\RabbitMqSqlBundle\Tests\DependencyInjection;

use \PHPUnit_Framework_TestCase as BaseTestCase;
use Ajir\RabbitMqSqlBundle\DependencyInjection\AjirRabbitMqSqlExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *
 */
class AjirRabbitMqSqlExtensionTest extends BaseTestCase
{
    /**
     * @var AjirRabbitMqSqlExtension
     */
    private $extension;

    /**
     *
     */
    public function testConstruct()
    {
        $ext = new AjirRabbitMqSqlExtension();

        $this->assertInstanceOf('Ajir\RabbitMqSqlBundle\DependencyInjection\AjirRabbitMqSqlExtension', $ext);
    }

    /**
     * @return AjirRabbitMqSqlExtension
     */
    protected function getExtension()
    {
        return new AjirRabbitMqSqlExtension();
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
