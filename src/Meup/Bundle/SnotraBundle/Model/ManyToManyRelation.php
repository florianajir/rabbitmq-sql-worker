<?php
namespace Meup\Bundle\SnotraBundle\Model;

use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformer;

/**
 * Class ManyToManyRelation
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class ManyToManyRelation extends AbstractRelation implements RelationInterface
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @var string
     */
    protected $joinTableName;

    /**
     * @var string
     */
    protected $inverseJoinColumnName;

    /**
     * @var string
     */
    protected $inverseJoinColumnReferencedColumnName;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $relation = $data[DataTransformer::RELATED_RELATION_KEY];
        $this->table = $relation[DataMapper::MAPPING_KEY_TABLE];
        $joinTable = $relation[DataMapper::RELATION_KEY_JOIN_TABLE];
        $this->joinTableName = $joinTable[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $joinColumn = $joinTable[DataMapper::RELATION_KEY_JOIN_COLUMN];
        $this->joinColumnName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $this->joinColumnReferencedColumnName =
            $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
        $inverseJoinColumn = $joinTable[DataMapper::RELATION_KEY_INVERSE_JOIN_COLUMN];
        $this->inverseJoinColumnName = $inverseJoinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $this->inverseJoinColumnReferencedColumnName =
            $inverseJoinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
        $this->entities = $data[DataTransformer::RELATED_DATA_KEY];
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @return string
     */
    public function getJoinTableName()
    {
        return $this->joinTableName;
    }

    /**
     * @return string
     */
    public function getInverseJoinColumnName()
    {
        return $this->inverseJoinColumnName;
    }

    /**
     * @return string
     */
    public function getInverseJoinColumnReferencedColumnName()
    {
        return $this->inverseJoinColumnReferencedColumnName;
    }
}
