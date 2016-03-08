<?php

namespace Ajir\RabbitMqSqlBundle\Tests\Factory;

use Ajir\RabbitMqSqlBundle\Factory\EntityFactory;

/**
 *
 */
class EntityFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $class = 'Ajir\RabbitMqSqlBundle\Model\Entity';

    /**
     *
     */
    public function testFactoryInterface()
    {
        $factory = new EntityFactory($this->class);

        $this->assertInstanceOf('Ajir\RabbitMqSqlBundle\Factory\EntityFactoryInterface', $factory);
    }

    /**
     *
     */
    public function testCreate()
    {
        $factory = new EntityFactory($this->class);

        $this->assertInstanceOf($this->class, $factory->create(array('_table' => 'test')));
    }

    /**
     *
     */
    public function testCreateAssumingException()
    {
        $this->setExpectedException('InvalidArgumentException');
        new EntityFactory('stdClass');
    }
}
