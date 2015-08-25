<?php
namespace Meup\Bundle\SnotraBundle\Model;

/**
 * Interface RelationInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
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
}
