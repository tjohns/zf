<?php

class Zend_Entity_DbMapper_IntegrationTest_University_OracleTest extends Zend_Entity_DbMapper_IntegrationTest_UniversityTestCase
{
    static private $adapter;

    protected function getConnection()
    {
        if(!defined('TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED') || TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED == false) {
            $this->markTestSkipped();
        }

        if(self::$adapter == null) {
            self::$adapter = Zend_Db::factory("oracle", array(
                "host" => TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME,
                "username" => TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME,
                "password" => TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD,
                "dbname" => TESTS_ZEND_DB_ADAPTER_ORACLE_SID,
            ));
        }
        return $this->createZendDbConnection(self::$adapter, TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME);
    }

    public function init(Zend_Entity_MetadataFactory_Code $mf)
    {

    }
}