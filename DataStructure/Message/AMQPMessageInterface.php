<?php

namespace Ajir\RabbitMqSqlBundle\DataStructure\Message;

interface AMQPMessageInterface
{
    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $index
     *
     * @return self
     */
    public function setIndex($index);

    /**
     * @return mixed
     */
    public function getIndex();

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param mixed $data
     *
     * @return self
     */
    public function setData($data);

    /**
     * @return mixed
     */
    public function getData();
}
