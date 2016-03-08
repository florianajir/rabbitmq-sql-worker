<?php
namespace Ajir\RabbitMqSqlBundle\Factory;

use InvalidArgumentException;
use ReflectionClass;

/**
 * Class EntityFactory
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
class EntityFactory implements EntityFactoryInterface
{
    const MODEL_INTERFACE = 'Ajir\RabbitMqSqlBundle\Model\EntityInterface';

    /**
     * @var ReflectionClass
     */
    protected $class;

    /**
     * @var RelationFactoryInterface
     */
    protected $relationFactory;

    /**
     * @param string $classname
     *
     * @throws InvalidArgumentException
     */
    public function __construct($classname)
    {
        $this->class = new ReflectionClass($classname);
        if (!$this->class->implementsInterface(self::MODEL_INTERFACE)) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        return $this->class->newInstance($data);
    }
}
