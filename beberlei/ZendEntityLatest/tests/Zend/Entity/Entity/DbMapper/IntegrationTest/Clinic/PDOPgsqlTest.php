<?php

class Zend_Entity_DbMapper_IntegrationTest_Clinic_PDOPgsqlTest extends Zend_Entity_DbMapper_IntegrationTest_ClinicTestCase
{
    protected function getConnection()
    {
        if(!defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED') || TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED == false) {
            $this->markTestSkipped();
        }

        $db = Zend_Db::factory("pdo_pgsql", array(
                "host" => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME,
                "username" => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME,
                "password" => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD,
                "dbname" => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE,
                'port' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PORT,
            ));
        return $this->createZendDbConnection($db, TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE);
    }
}