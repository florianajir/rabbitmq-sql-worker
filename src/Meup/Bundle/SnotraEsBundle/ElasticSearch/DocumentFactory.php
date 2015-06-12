<?php

namespace Meup\Bundle\SnotraEsBundle\ElasticSearch;

/**
 *
 */
class DocumentFactory implements DocumentFactoryInterface
{

    const BASE_CLASS = 'Meup\Bundle\SnotraEsBundle\ElasticSearch\Document';

    /**
     * @var ReflectionClass
     */
    private $class;

    /**
     * @param string $class
     *
     * @throws InvalidArgumentException
     * @return void
     */
    public function __construct($class = self::BASE_CLASS)
    {
        $this->class = new \ReflectionClass($class);

        if (self::BASE_CLASS!=$this->class->getName() && !$this->class->isSubclassOf(self::BASE_CLASS)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "'%s' should be an instance of %s",
                    $this->class->getName(),
                    self::BASE_CLASS
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create($id, $object)
    {
        return $this->class->newInstance($id, $object);
    }
}
