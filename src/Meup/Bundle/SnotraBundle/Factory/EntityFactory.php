<?php
namespace Meup\Bundle\SnotraBundle\Factory;

use InvalidArgumentException;
use ReflectionClass;

/**
 * Class EntityFactory
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class EntityFactory implements EntityFactoryInterface
{
    const MODEL_INTERFACE = 'Meup\Bundle\SnotraBundle\Model\EntityInterface';

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
    public function create($table, array $data)
    {
        return $this->class->newInstanceArgs(array($table, $data));
    }
}
