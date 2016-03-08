<?php

namespace Ajir\RabbitMqSqlBundle\DataStructure\Message;

/**
 *
 */
class AMQPMessageFactory implements AMQPMessageFactoryInterface
{
    /**
     * The default AMQPMessage class.
     */
    const DEFAULT_CLASS = 'Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessage';

    /**
     * @var ReflectionClass
     */
    protected $class;

    /**
     * @throws InvalidArgumentException
     *
     * @param string $class
     */
    public function __construct($class = self::DEFAULT_CLASS)
    {
        $interface   = 'Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessageInterface';
        $this->class = new \ReflectionClass($class);

        if (!$this->class->implementsInterface($interface)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "'%s' should implements %s",
                    $this->class->getName(),
                    $interface
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        return $this->class->newInstance();
    }
}
