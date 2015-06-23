<?php
namespace Meup\Bundle\SnotraBundle\Provider;

use Doctrine\DBAL\Connection;

/**
 * Class SqlProvider
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SqlProvider
{
    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param string $table
     * @param array  $data
     */
    public function insert($table, array $data)
    {
        $this->conn->insert($table, $data);
    }

    /**
     * @param string $table
     * @param array  $data
     * @param array  $identifier
     */
    public function update($table, array $data, array $identifier)
    {
        $this->conn->update($table, $data, $identifier);
    }
}
