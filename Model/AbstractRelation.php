<?php
namespace Ajir\RabbitMqSqlBundle\Model;

use Ajir\RabbitMqSqlBundle\DataMapper\DataMapper;
use Ajir\RabbitMqSqlBundle\DataTransformer\DataTransformer;

/**
 * Class AbstractRelation
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
abstract class AbstractRelation implements RelationInterface
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $joinColumnName;

    /**
     * @var string
     */
    protected $joinColumnReferencedColumnName;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * AbstractRelation constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $relation = $data[DataTransformer::RELATED_RELATION_KEY];
        $this->table = $relation[DataMapper::MAPPING_KEY_TABLE];
        $this->entityName = $relation[DataMapper::RELATION_KEY_TARGET_ENTITY];
    }


    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getJoinColumnName()
    {
        return $this->joinColumnName;
    }

    /**
     * @return string
     */
    public function getJoinColumnReferencedColumnName()
    {
        return $this->joinColumnReferencedColumnName;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }
}
