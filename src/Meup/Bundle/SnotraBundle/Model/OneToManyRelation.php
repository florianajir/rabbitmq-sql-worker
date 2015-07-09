<?php
namespace Meup\Bundle\SnotraBundle\Model;

use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformer;

/**
 * Class OneToManyRelation
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class OneToManyRelation extends AbstractRelation implements RelationInterface
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $relation = $data[DataTransformer::RELATED_RELATION_KEY];
        $this->table = $relation[DataMapper::MAPPING_KEY_TABLE];
        $joinColumn = $relation[DataMapper::RELATION_KEY_JOIN_COLUMN];
        $this->joinColumnName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $this->joinColumnReferencedColumnName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
        $this->entities = $data[DataTransformer::RELATED_DATA_KEY];
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
