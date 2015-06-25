<?php
namespace Meup\Bundle\SnotraBundle\Provider;

use Doctrine\DBAL\Connection;

/**
 * Class SqlProvider
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SqlProvider implements SqlProviderInterface
{
    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @param Connection $conn
     * @param string     $env
     */
    public function __construct(Connection $conn, $env)
    {
        $this->conn = $conn;
        //TODO remove after dev
        if ($env === 'dev') {
            $this->conn->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        }
    }

    /**
     * @param string $table
     * @param array  $data
     * @param array  $identifier
     *
     * @return integer The number of affected rows.
     */
    public function insertOrUpdateIfExists($table, array $data, array $identifier = array())
    {
        if (!empty($identifier)) {
            $exists = $this->exists($table, key($identifier), current($identifier));
            if ($exists) {
                return $this->update($table, $data, $identifier);
            }
        }

        return $this->insert($table, $data);
    }

    /**
     * @param string $table
     * @param string $identifier
     * @param string $value
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function exists($table, $identifier, $value)
    {
        $sth = $this->conn->executeQuery("SELECT count(*) FROM `{$table}` WHERE `{$identifier}` = '{$value}'");
        $exist = $sth->fetchColumn();

        return !empty($exist);
    }

    /**
     * @param string $table
     * @param array  $data
     * @param array  $identifier
     *
     * @return integer The number of affected rows.
     */
    public function update($table, array $data, array $identifier)
    {
        return $this->conn->update($table, $data, $identifier);
    }

    /**
     * @param string $table
     * @param array  $data
     *
     * @return integer The number of affected rows.
     */
    public function insert($table, array $data)
    {
        return $this->conn->insert($table, $data);
    }
}
