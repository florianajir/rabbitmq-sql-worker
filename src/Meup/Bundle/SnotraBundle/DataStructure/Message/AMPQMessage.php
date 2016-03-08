<?php

namespace Meup\DataStructure\Message;

use JMS\Serializer\Annotation as JMS;

/**
 *
 */
class AMQPMessage implements AMQPMessageInterface
{
    /**
     * @var mixed
     * @JMS\Type("string")
     */
    private $id;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $index;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $type;

    /**
     * @var mixed
     * @JMS\Type("string")
     */
    private $data;

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
