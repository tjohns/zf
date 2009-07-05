<?php

require_once "AbstractTestCase.php";

class Zend_Test_PHPUnit_Database_Integration_MysqlIntegrationTest extends Zend_Test_PHPUnit_Database_Integration_AbstractTestCase
{
    public function setUp()
    {
        if (!extension_loaded('pdo')) {
            $this->markTestSkipped('PDO is required for this test.');
        }

        if(!in_array('mysql', PDO::getAvailableDrivers())) {
            $this->markTestSkipped('Mysql is not included in PDO in this PHP installation.');
        }

        $params = array(
            'host' => TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD,
            'dbname' => TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE,
        );

        $this->dbAdapter = Zend_Db::factory('pdo_mysql', $params);
        $this->dbAdapter->query("DROP TABLE foo");
        $this->dbAdapter->query("DROP TABLE bar");
        $this->dbAdapter->query(
            'CREATE TABLE foo (id INT(10) AUTO_INCREMENT PRIMARY KEY, foo VARCHAR(255), bar VARCHAR(255), baz VARCHAR(255)) AUTO_INCREMENT=1'
        );
        $this->dbAdapter->query(
            'CREATE TABLE bar (id INT(10) AUTO_INCREMENT PRIMARY KEY, foo VARCHAR(255), bar VARCHAR(255), baz VARCHAR(255)) AUTO_INCREMENT=1'
        );
    }
}