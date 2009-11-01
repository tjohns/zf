<?php

class Zend_Entity_DbMapper_IntegrationTest_University_MysqliTest extends Zend_Entity_DbMapper_IntegrationTest_UniversityTestCase
{
    protected function getConnection()
    {
        if(!defined('TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED') || TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED == false) {
            $this->markTestSkipped();
        }

        $db = Zend_Db::factory("mysqli", array(
                "host" => TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME,
                "username" => TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME,
                "password" => TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD,
                "dbname" => TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE
            ));
        return $this->createZendDbConnection($db, TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE);
    }
}