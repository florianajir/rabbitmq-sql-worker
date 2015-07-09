<?php
namespace Meup\Bundle\SnotraBundle\Provider;

use Doctrine\DBAL\DBALException;

/**
 * Interface ProviderInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface ProviderInterface
{
    /**
     * Delete a record
     *
     * @param string $table
     * @param array  $conditions
     */
    public function delete($table, $conditions);

    /**
     * Return true if the record exists
     *
     * @param string $table      The expression of the table to update quoted or unquoted.
     * @param string $identifier The update criteria. An associative array containing column-value pairs.
     * @param string $value
     *
     * @return bool
     *
     * @throws DBALException
     */
    public function exists($table, $identifier, $value);

    /**
     * @param string $table
     * @param string $column
     * @param string $where
     * @param string $value
     *
     * @return string
     */
    public function getColumnValueWhere($table, $column, $where, $value);

    /**
     * Insert a record in sql database
     *
     * @param string $table The expression of the table to update quoted or unquoted.
     * @param array  $data  An associative array containing column-value pairs.
     *
     * @return integer last insert id
     */
    public function insert($table, array $data);

    /**
     * Insert or update a record if exists
     *
     * @param string $table      The expression of the table to update quoted or unquoted.
     * @param array  $data       An associative array containing column-value pairs.
     * @param array  $identifier The update criteria. An associative array containing column-value pairs.
     *
     * @return integer last insert id
     */
    public function insertOrUpdateIfExists($table, array $data, array $identifier = null);

    /**
     * Update a record in sql database
     *
     * @param string $table      The expression of the table to update quoted or unquoted.
     * @param array  $data       An associative array containing column-value pairs.
     * @param array  $identifier The update criteria. An associative array containing column-value pairs.
     *
     * @return integer last insert id
     */
    public function update($table, array $data, array $identifier);
}
