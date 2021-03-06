<?php
namespace Ajir\RabbitMqSqlBundle\Provider;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Logging\EchoSQLLogger;

/**
 * Class SqlProvider
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
class SqlProvider implements ProviderInterface
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
        if ($env !== 'prod') {
            $this->conn->getConfiguration()->setSQLLogger(new EchoSQLLogger());
        }
    }

    /**
     * @param string $table
     * @param array  $conditions
     *
     * @return integer The number of affected rows.
     *
     * @throws InvalidArgumentException
     */
    public function delete($table, $conditions)
    {
        return $this->conn->delete($table, $conditions);
    }

    /**
     * @param string $table
     * @param array  $data
     * @param array  $identifier
     *
     * @return integer last insert id
     */
    public function insertOrUpdateIfExists($table, array $data, array $identifier = null)
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
     * @return boolean
     *
     * @throws DBALException
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
     * @return integer last insert id
     */
    public function update($table, array $data, array $identifier)
    {
        $data = $this->quoteIdentifiers($data);
        $this->conn->update($table, $data, $identifier);

        return $this->conn->lastInsertId();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function quoteIdentifiers($data)
    {
        $prepared = array();
        foreach ($data as $id => $value) {
            $quoted = $this->conn->quoteIdentifier($id);
            $prepared[$quoted] = $value;
        }

        return $prepared;
    }

    /**
     * @param string $table
     * @param array  $data
     *
     * @return integer last insert id
     */
    public function insert($table, array $data)
    {
        $data = $this->quoteIdentifiers($data);
        $this->conn->insert($table, $data);

        return $this->conn->lastInsertId();
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $where
     * @param string $value
     *
     * @return string
     *
     * @throws DBALException
     */
    public function getColumnValueWhere($table, $column, $where, $value)
    {
        $sth = $this->conn->executeQuery("SELECT `{$column}` FROM `{$table}` WHERE `{$where}` = '{$value}'");

        return $sth->fetchColumn();
    }
}
