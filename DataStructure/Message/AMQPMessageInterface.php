<?php

namespace Ajir\RabbitMqSqlBundle\DataStructure\Message;

interface AMQPMessageInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return self
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getIndex();

    /**
     * @param string $index
     * @return self
     */
    public function setIndex($index);

    /**
     * @param string $type
     * @return self
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getData();

    /**
     * @param string $data
     * @return self
     */
    public function setData($data);
}
