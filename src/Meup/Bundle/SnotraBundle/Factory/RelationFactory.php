<?php
namespace Meup\Bundle\SnotraBundle\Factory;

use InvalidArgumentException;
use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\Model\OneToOneRelation;
use Meup\Bundle\SnotraBundle\Model\RelationInterface;
use ReflectionClass;

/**
 * Class RelationFactory
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class RelationFactory implements RelationFactoryInterface
{
    const RELATION_FACTORY = 'Meup\Bundle\SnotraBundle\Model\RelationInterface';

    /**
     * @var ReflectionClass
     */
    protected $oneToOneClass;

    /**
     * @var ReflectionClass
     */
    protected $oneToManyClass;

    /**
     * @var ReflectionClass
     */
    protected $manyToOneClass;

    /**
     * @var ReflectionClass
     */
    protected $manyToManyClass;

    /**
     * @param string                        $oneToOneClassName
     * @param string                        $oneToManyClassName
     * @param string                        $manyToOneClassName
     * @param string                        $manyToManyClassName
     */
    public function __construct(
        $oneToOneClassName,
        $oneToManyClassName,
        $manyToOneClassName,
        $manyToManyClassName
    ) {
        $this->oneToOneClass = new ReflectionClass($oneToOneClassName);
        if (!$this->oneToOneClass->implementsInterface(self::RELATION_FACTORY)) {
            throw new InvalidArgumentException();
        }
        $this->oneToManyClass = new ReflectionClass($oneToManyClassName);
        if (!$this->oneToManyClass->implementsInterface(self::RELATION_FACTORY)) {
            throw new InvalidArgumentException();
        }
        $this->manyToOneClass = new ReflectionClass($manyToOneClassName);
        if (!$this->manyToOneClass->implementsInterface(self::RELATION_FACTORY)) {
            throw new InvalidArgumentException();
        }
        $this->manyToManyClass = new ReflectionClass($manyToManyClassName);
        if (!$this->manyToManyClass->implementsInterface(self::RELATION_FACTORY)) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @param string $relation
     * @param array  $data
     *
     * @return RelationInterface
     *
     * @throws InvalidArgumentException
     */
    public function create($relation, $data)
    {
        switch ($relation) {
            case DataMapper::RELATION_ONE_TO_ONE:
                return $this->createOneToOne($data);

            case DataMapper::RELATION_ONE_TO_MANY:
                return $this->createOneToMany($data);

            case DataMapper::RELATION_MANY_TO_ONE:
                return $this->createManyToOne($data);

            case DataMapper::RELATION_MANY_TO_MANY:
                return $this->createManyToMany($data);

            default:
                throw new InvalidArgumentException("Invalid relation : $relation");
        }
    }

    /**
     * @param array $data
     *
     * @return OneToOneRelation
     */
    protected function createOneToOne($data)
    {
        return $this->oneToOneClass->newInstanceArgs(array($data));
    }

    /**
     * @param array $data
     *
     * @return RelationInterface
     */
    protected function createOneToMany($data)
    {
        return $this->oneToManyClass->newInstanceArgs(array($data));
    }

    /**
     * @param array $data
     *
     * @return RelationInterface
     */
    protected function createManyToOne($data)
    {
        return $this->manyToOneClass->newInstanceArgs(array($data));
    }

    /**
     * @param array $data
     *
     * @return RelationInterface
     */
    protected function createManyToMany($data)
    {
        return $this->manyToManyClass->newInstanceArgs(array($data));
    }
}