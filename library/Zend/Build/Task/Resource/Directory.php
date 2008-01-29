<?php

require_once 'Zend/Build/Task/Resource/Abstract.php';

class Zend_Build_Task_Resource_Directory extends Zend_Build_Task_Resource_Abstract
{
    public function getName()
    {
        return 'directory';
    }
    
    public function satisfyDependencies()
    {
        if (!isset($this->_parameters['directoryName']) || !is_string($this->_parameters['directoryName'])) {
            throw new Zend_Build_Task_Action_Exception('Parameter (directoryName) was not set or is not a string');
        }
    }
    
    public function createAction()
    {
        $directoryName = $this->_parameters['directoryName'];
        mkdir($directoryName);
        return true;
    }
    
    public function deleteAction()
    {
        $directoryName = $this->_parameters['directoryName'];
        $this->_deleteFile($directoryName);
    }
    
    public function rollback()
    {
        $directoryName = $this->_parameters['directoryName'];
        $this->_deleteFile($directoryName);
    }
    
    protected function _deleteFile($directoryName)
    {
        rmdir($directoryName);
    }
    
}