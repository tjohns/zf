<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Abstract.php';

abstract class Zend_Tool_Project_Context_Filesystem_File extends Zend_Tool_Project_Context_Filesystem_Abstract 
{
    
    public function setFileName($fileName)
    {
        $this->_filesystemName = $fileName;
    }
    
    public function setResource($resource)
    {
        $this->_resource = $resource;
        $this->_resource->setAppendable(false);
    }


    public function create()
    {
        file_put_contents($this->getPath(), $this->getContents());
    }
    
    public function recursivelyCreate()
    {
        $this->create();
    }
    
    public function delete($recurseChildren = true)
    {
        unlink($this->getPath());
        $this->_resource->setDeleted(true);
    }
    
    public function recursivelyDelete()
    {
        $this->delete();
    }
    
    /*
    protected function _write()
    {
        if (!$this->_enabled) {
            return;
        }
        
        file_put_contents($this->getFileName(), $this->getContents());
    }
    */
    
    public function getContents()
    {
        return null;
    }
    
}