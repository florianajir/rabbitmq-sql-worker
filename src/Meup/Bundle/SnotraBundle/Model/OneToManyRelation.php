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
     * @var array
     */
    protected $references;

    /**
     * @var bool
     */
    protected $removeReferenced;

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
        $this->entities = $data[DataTransformer::RELATED_DATA_KEY];
        $this->references = isset($relation[DataMapper::REFERENCES_KEY]) ?
            $relation[DataMapper::REFERENCES_KEY] : array();
        $this->removeReferenced = isset($relation[DataMapper::REMOVE_REFERENCED_KEY])
            && $relation[DataMapper::REMOVE_REFERENCED_KEY] == 'true';
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @return boolean
     */
    public function isRemoveReferenced()
    {
        return $this->removeReferenced;
    }
}
