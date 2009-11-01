<?php

class Zend_Entity_DbMapper_IntegrationTest_University_PdoOciTest extends Zend_Entity_DbMapper_IntegrationTest_UniversityTestCase
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    static private $adapter;

    protected function getConnection()
    {
        if(!defined('TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED') || TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED == false) {
            $this->markTestSkipped();
        }

        if(self::$adapter == null) {
            self::$adapter = Zend_Db::factory("pdo_oci", array(
                "host" => TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME,
                "username" => TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME,
                "password" => TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD,
                "dbname" => TESTS_ZEND_DB_ADAPTER_ORACLE_SID,
            ));
            self::$adapter->setProfiler(true);
        }
        return $this->createZendDbConnection(self::$adapter, TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME);
    }

    public function init(Zend_Entity_MetadataFactory_Code $mf)
    {
        $gen = new Zend_Db_Mapper_Id_Increment();
        $mf->getDefinitionByEntityName("ZendEntity_Course")->primaryKey->setGenerator($gen);
        $mf->getDefinitionByEntityName("ZendEntity_Professor")->primaryKey->setGenerator($gen);
        $mf->getDefinitionByEntityName("ZendEntity_Student")->primaryKey->setGenerator($gen);
    }

    public function tearDown()
    {
        #var_dump(self::$adapter->getProfiler()->getQueryProfiles());
        #self::$adapter->getProfiler()->clear();
    }
}