<?php
namespace Ajir\RabbitMqSqlBundle\Persister;

/**
 * Interface PersisterInterface
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
interface PersisterInterface
{
    /**
     * Persist data
     *
     * @param array $data An associative array containing column-value pairs.
     */
    public function persist(array $data);
}
