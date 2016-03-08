<?php
namespace Ajir\RabbitMqSqlBundle\Factory;

use Ajir\RabbitMqSqlBundle\Model\ManyToManyRelation;
use Ajir\RabbitMqSqlBundle\Model\ManyToOneRelation;
use Ajir\RabbitMqSqlBundle\Model\OneToManyRelation;
use Ajir\RabbitMqSqlBundle\Model\OneToOneRelation;
use Ajir\RabbitMqSqlBundle\Model\RelationInterface;

/**
 * Interface RelationFactoryInterface
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
interface RelationFactoryInterface
{
    /**
     * @param string $relation
     * @param array $data
     *
     * @return RelationInterface|ManyToManyRelation|ManyToOneRelation|OneToManyRelation|OneToOneRelation
     */
    public function create($relation, $data);
}
