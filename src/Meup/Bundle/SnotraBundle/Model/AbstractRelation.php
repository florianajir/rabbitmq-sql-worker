<?php
namespace Meup\Bundle\SnotraBundle\Model;

/**
 * Class AbstractRelation
 *
 * @author florianajir <florian@1001pharmacies.com>
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
}
