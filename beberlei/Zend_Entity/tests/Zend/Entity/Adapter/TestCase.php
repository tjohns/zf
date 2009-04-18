<?php

abstract class Zend_Entity_Adapter_TestCase extends PHPUnit_Framework_TestCase
{
    protected $_adapter = null;

    protected $_adapterOptions = array();

    /**
     * Db Adapter
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    public function setUp()
    {
        $this->_db = $this->createConnection();
        $tables = $this->getConnection()->listTables();
        foreach($tables AS $table) {
            $this->getConnection()->delete($table);
        }

        $this->setUpDatabase();
    }

    public function tearDown()
    {
        $this->tearDownDatabase();
        $this->_db->closeConnection();
    }

    protected function createConnection()
    {
        return Zend_Db::factory($this->_adapter, $this->_adapterOptions);
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getConnection()
    {
        return $this->_db;
    }

    protected function setUpDatabase()
    {
        
    }

    protected function tearDownDatabase()
    {
        $tables = $this->getConnection()->listTables();
        foreach($tables AS $table) {
            $this->getConnection()->delete($table);
        }
    }
}