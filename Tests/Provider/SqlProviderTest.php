<?php
namespace Ajir\RabbitMqSqlBundle\Tests\Provider;

use Doctrine\DBAL\Logging\EchoSQLLogger;
use Ajir\RabbitMqSqlBundle\Provider\SqlProvider;

/**
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
class SqlProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testInstanciateWithDebugLogger()
    {
        $configMock = $this->getConfigurationMock();
        $configMock->expects($this->once())
            ->method('setSQLLogger')
            ->with(new EchoSQLLogger())
        ;
        $conn = $this->getConnectionMock();
        $conn->expects($this->once())
            ->method('getConfiguration')
            ->with()
            ->will($this->returnValue($configMock));
        new SqlProvider($conn, 'test');
    }

    /**
     *
     */
    public function testDelete()
    {
        $table = 'test';
        $conditions = array('id' => 1);
        $returned = 1;
        $conn = $this->getConnectionMock();
        $conn->expects($this->once())
            ->method('delete')
            ->with($table, $conditions)
            ->will($this->returnValue($returned));
        $sqlProvider = new SqlProvider($conn, 'prod');
        $result = $sqlProvider->delete($table, $conditions);
        $this->assertEquals($returned, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\DBAL\Connection
     */
    private function getConnectionMock()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        return $conn;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\DBAL\Configuration
     */
    private function getConfigurationMock()
    {
        $config = $this->getMockBuilder('\Doctrine\DBAL\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        return $config;
    }

    /**
     *
     */
    public function testExists()
    {
        $exists = '1';
        $table = 'test';
        $identifier = 'id';
        $value = 1;
        $conn = $this->getConnectionMock();
        $sth = $this->getStatementMock();
        $sth->expects($this->once())
            ->method('fetchColumn')
            ->will($this->returnValue($exists));
        $conn->expects($this->once())
            ->method('executeQuery')
            ->with("SELECT count(*) FROM `{$table}` WHERE `{$identifier}` = '{$value}'")
            ->will($this->returnValue($sth));
        $sqlProvider = new SqlProvider($conn, 'prod');
        $result = $sqlProvider->exists($table, $identifier, $value);
        $this->assertEquals($exists, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\DBAL\Statement
     */
    private function getStatementMock()
    {
        $sth = $this->getMockBuilder('\Doctrine\DBAL\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        return $sth;
    }

    /**
     *
     */
    public function testUpdate()
    {
        $data = array(
            'id'   => '1',
            'name' => 'test'
        );
        $table = 'test';
        $identifier = array('id' => 1);
        $conn = $this->getConnectionMock();
        $conn->expects($this->exactly(count($data)))
            ->method('quoteIdentifier')
            ->with($this->anything())
            ->will(
                $this->returnCallback(
                    'Ajir\RabbitMqSqlBundle\Tests\Provider\SqlProviderTest::quoteIdentifierCallback'
                )
            );
        $conn->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue($data['id']));
        $sqlProvider = new SqlProvider($conn, 'prod');
        $result = $sqlProvider->update($table, $data, $identifier);
        $this->assertEquals($data['id'], $result);
    }

    public function quoteIdentifierCallback()
    {
        $args = func_get_args();

        return "`{$args[0]}`";
    }

    /**
     *
     */
    public function testInsert()
    {
        $lastInsertId = '1';
        $data = array(
            'name' => 'test'
        );
        $table = 'test';
        $conn = $this->getConnectionMock();
        $conn->expects($this->exactly(count($data)))
            ->method('quoteIdentifier')
            ->with($this->anything())
            ->will(
                $this->returnCallback(
                    'Ajir\RabbitMqSqlBundle\Tests\Provider\SqlProviderTest::quoteIdentifierCallback'
                )
            );
        $conn->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue($lastInsertId));
        $sqlProvider = new SqlProvider($conn, 'prod');
        $result = $sqlProvider->insert($table, $data);
        $this->assertEquals($lastInsertId, $result);
    }

    /**
     *
     */
    public function testInsertOrUpdateWithoutIdentifier()
    {
        $data = array(
            'id'   => '1',
            'name' => 'test'
        );
        $table = 'test';
        $conn = $this->getConnectionMock();
        $conn->expects($this->exactly(count($data)))
            ->method('quoteIdentifier')
            ->with($this->anything())
            ->will(
                $this->returnCallback(
                    'Ajir\RabbitMqSqlBundle\Tests\Provider\SqlProviderTest::quoteIdentifierCallback'
                )
            );
        $conn->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue($data['id']));
        $sqlProvider = new SqlProvider($conn, 'prod');
        $result = $sqlProvider->insertOrUpdateIfExists($table, $data);
        $this->assertEquals($data['id'], $result);
    }

    /**
     *
     */
    public function testInsertOrUpdateWithIdentifier()
    {
        $exists = '1';
        $data = array(
            'id'   => '1',
            'name' => 'test'
        );
        $table = 'test';
        $identifier = array('id' => 1);
        $conn = $this->getConnectionMock();
        $sth = $this->getStatementMock();
        $sth->expects($this->once())
            ->method('fetchColumn')
            ->will($this->returnValue($exists));
        $conn->expects($this->once())
            ->method('executeQuery')
            ->with("SELECT count(*) FROM `{$table}` WHERE `id` = '1'")
            ->will($this->returnValue($sth));
        $conn->expects($this->exactly(count($data)))
            ->method('quoteIdentifier')
            ->with($this->anything())
            ->will(
                $this->returnCallback(
                    'Ajir\RabbitMqSqlBundle\Tests\Provider\SqlProviderTest::quoteIdentifierCallback'
                )
            );
        $conn->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue($data['id']));
        $sqlProvider = new SqlProvider($conn, 'prod');
        $result = $sqlProvider->insertOrUpdateIfExists($table, $data, $identifier);
        $this->assertEquals($data['id'], $result);
    }

    /**
     *
     */
    public function testGetColumnValueWhere()
    {
        $column = 'name';
        $where = 'id';
        $value = '1';
        $table = 'test';
        $fetch = 'test';
        $conn = $this->getConnectionMock();
        $sth = $this->getStatementMock();
        $sth->expects($this->once())
            ->method('fetchColumn')
            ->will($this->returnValue($fetch));
        $conn->expects($this->once())
            ->method('executeQuery')
            ->with("SELECT `{$column}` FROM `{$table}` WHERE `{$where}` = '{$value}'")
            ->will($this->returnValue($sth));
        $sqlProvider = new SqlProvider($conn, 'prod');
        $result = $sqlProvider->getColumnValueWhere($table, $column, $where, $value);
        $this->assertEquals($fetch, $result);
    }
}
