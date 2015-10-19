<?php
namespace Meup\Bundle\SnotraBundle\Model;

use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformer;

/**
 * Class ManyToOneRelation
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class ManyToOneRelation extends AbstractRelation implements RelationInterface
{
    /**
     * @var array
     */
    protected $entity;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $relation = $data[DataTransformer::RELATED_RELATION_KEY];
        $joinColumn = $relation[DataMapper::RELATION_KEY_JOIN_COLUMN];
        $this->joinColumnName = $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_NAME];
        $this->joinColumnReferencedColumnName =
            $joinColumn[DataMapper::RELATION_KEY_JOIN_COLUMN_REFERENCED_COLUMN_NAME];
        $this->entity = $data[DataTransformer::RELATED_DATA_KEY][$this->entityName];
    }

    /**
     * @return array
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
