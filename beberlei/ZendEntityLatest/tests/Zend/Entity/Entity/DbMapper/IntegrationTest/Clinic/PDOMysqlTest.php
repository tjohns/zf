<?php

class Zend_Entity_DbMapper_IntegrationTest_Clinic_PDOMysqlTest extends Zend_Entity_DbMapper_IntegrationTest_ClinicTestCase
{
    protected function getConnection()
    {
        if(!defined('TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED') || TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED == false) {
            $this->markTestSkipped();
        }

        $db = Zend_Db::factory("pdo_mysql", array(
                "host" => TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME,
                "username" => TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME,
                "password" => TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD,
                "dbname" => TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE
            ));
        return $this->createZendDbConnection($db, TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE);
    }
}