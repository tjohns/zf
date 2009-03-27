<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

class Zend_Tool_Project_Context_Zf_DbTableFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    public function getName()
    {
        return 'DbTableFile';
    }
    
    
    /*
    protected $_dbTableName;
    

    
    public function getPersistentAttributes()
    {
        return array('dbTableName' => $this->_dbTableName);
    }
    
    public function setDbTableName($dbTableName)
    {
        $this->_dbTableName = $dbTableName;
        $this->_filesystemName = $dbTableName . '.php';
    }

    public function getDbTableName()
    {
        return $this->_dbTableName;
    }
    */
    
}
