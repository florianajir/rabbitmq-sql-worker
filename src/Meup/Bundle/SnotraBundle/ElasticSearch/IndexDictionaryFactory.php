<?php
namespace Meup\Bundle\SnotraBundle\ElasticSearch;

use InvalidArgumentException;
use ReflectionClass;

/**
 *
 */
class IndexDictionaryFactory implements IndexDictionaryFactoryInterface
{
    const DEFAULT_CLASS = 'Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionary';

    /**
     * @var ReflectionClass
     */
    private $class;

    /**
     * @var string
     */
    private $index;

    /**
     * @param string $indexClassName
     * @param string $class
     *
     * @throws InvalidArgumentException
     */
    public function __construct($indexClassName, $class = self::DEFAULT_CLASS)
    {
        $interface   = 'Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionaryInterface';
        $this->class = new ReflectionClass($class);
        $this->index = $indexClassName;

        if (!$this->class->implementsInterface($interface)) {
            throw new InvalidArgumentException(
                sprintf(
                    "'%s' should impelments %s",
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
        return $this->class->newInstance($this->index);
    }
}
