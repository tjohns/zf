<?php

require_once 'Zend/Build/Task/Resource/Abstract.php';

class Zend_Build_Task_Resource_File extends Zend_Build_Task_Resource_Abstract
{
    
    public function getName()
    {
        return 'file';
    }

    public function satisfyDependencies()
    {
        if (!isset($this->_parameters['fileName']) || !is_string($this->_parameters['fileName'])) {
            throw new Zend_Build_Task_Action_Exception('Parameter (fileName) was not set or is not a string');
        }
    }
    
    public function createAction()
    {
        $fileName = $this->_parameters['fileName'];
        $fileName = $this->_parameters['fileContents'];
        file_put_contents($fileName, $fileContents);
        return true;
    }
    
    public function deleteAction()
    {
        $fileName = $this->_parameters['fileName'];
        $this->_deleteFile($fileName);
    }
    
    public function rollback()
    {
        $fileName = $this->_parameters['fileName'];
        $this->_deleteFile($fileName);
    }
    
    protected function _deleteFile($fileName)
    {
        unlink($this->_parameters[$fileName]);
    }
    
}
