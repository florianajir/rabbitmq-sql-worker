<?php
namespace Ajir\RabbitMqSqlBundle\Model;

/**
 * Interface RelationInterface
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
interface RelationInterface
{
    /**
     * @return string
     */
    public function getTable();

    /**
     * @return string
     */
    public function getJoinColumnName();

    /**
     * @return string
     */
    public function getJoinColumnReferencedColumnName();

    /**
     * @return string
     */
    public function getEntityName();
}
