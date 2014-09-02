<?php

namespace Meup\Bundle\SnotraBundle\ElasticSearch;

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
     * @param string $class
     * @param string $indexClassName
     *
     * @throws InvalidArgumentException
     * @return void
     */
    public function __construct($class = self::DEFAULT_CLASS, $indexClassName)
    {
        $interface   = 'Meup\Bundle\SnotraBundle\ElasticSearch\IndexDictionaryInterface';
        $this->class = new \ReflectionClass($class);
        $this->index = $indexClassName;

        if (!$this->class->implementsInterface($interface)) {
            throw new \InvalidArgumentException(
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
