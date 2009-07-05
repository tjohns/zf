<?php

require_once "AbstractTestCase.php";

class Zend_Test_PHPUnit_Database_Integration_SqLiteIntegrationTest extends Zend_Test_PHPUnit_Database_Integration_AbstractTestCase
{
    public function setUp()
    {
        if (!extension_loaded('pdo')) {
            $this->markTestSkipped('PDO is required for this test.');
        }

        if(!in_array('sqlite', PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SqLite is not included in PDO in this PHP installation.');
        }

        $this->dbAdapter = Zend_Db::factory('pdo_sqlite', array('dbname' => ':memory:'));
        $this->dbAdapter->query(
            'CREATE TABLE "foo" (id INTEGER PRIMARY KEY AUTOINCREMENT, foo VARCHAR, bar VARCHAR, baz VARCHAR)'
        );
        $this->dbAdapter->query(
            'CREATE TABLE "bar" (id INTEGER PRIMARY KEY AUTOINCREMENT, foo VARCHAR, bar VARCHAR, baz VARCHAR)'
        );
    }
}