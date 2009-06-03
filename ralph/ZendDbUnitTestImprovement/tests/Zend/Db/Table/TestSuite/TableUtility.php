<?php

require_once 'Zend/Db/Table/AbstractTestCase.php';

class Zend_Db_Table_TestSuite_TableUtility
{
    
    protected $_dbAdapter = null;
    protected $_runtimeIncludePath = null;
    
    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
    }
    
    public function getTableById($tableId, $options = array())
    {
        $mapping = array(
            'Accounts'     => 'My_ZendDbTable_TableAccounts',
            'Bugs'         => 'My_ZendDbTable_TableBugs',
            'BugsProducts' => 'My_ZendDbTable_TableBugsProducts',
            'Products'     => 'My_ZendDbTable_TableProducts'
            );
            
        if (array_key_exists($tableId, $mapping)) {
            return $this->getTable($mapping[$tableId], $options);
        }
            
        throw new Exception('A table with the id ' . $tableId . ' does not exist.');
    }
    
    public function getTable($tableClass, $options = array())
    {
        if (is_array($options) && !isset($options['db'])) {
            $options['db'] = $this->_dbAdapter;
        }
        if (!class_exists($tableClass)) {
            $this->useMyIncludePath();
            try {
                Zend_Loader::loadClass($tableClass);
            } catch (Exception $e) {
                $this->restoreIncludePath();
                throw $e;
            }
            $this->restoreIncludePath();
        }
        $table = new $tableClass($options);
        return $table;
    }
    
    public function useMyIncludePath()
    {
        $this->_runtimeIncludePath = get_include_path();
        set_include_path(dirname(__FILE__) . '/../_files/' . PATH_SEPARATOR . $this->_runtimeIncludePath);
    }
    
    public function isOriginalIncludePath()
    {
        return (is_null($this->_runtimeIncludePath));
    }
    
    public function restoreIncludePath()
    {
        set_include_path($this->_runtimeIncludePath);
        $this->_runtimeIncludePath = null;
    }
    
    public function __get($name)
    {
        return $this->getTable($name);
    }
}