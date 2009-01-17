<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Abstract.php';

abstract class Zend_Tool_Project_Context_Filesystem_File extends Zend_Tool_Project_Context_Filesystem_Abstract 
{
    
    public function init()
    {
        //@todo check to ensure that this 'file' resource has no children
        parent::init();
    }
    
    /**
     * @todo determine if this is needed
     *
     * @param unknown_type $fileName
     */
//    public function setFileName($fileName)
//    {
//        $this->_filesystemName = $fileName;
//    }
    
    public function setResource($resource)
    {
        $this->_resource = $resource;
        $this->_resource->setAppendable(false);
    }

    public function create()
    {
        file_put_contents($this->getPath(), $this->getContents());
    }
    
    public function delete()
    {
        unlink($this->getPath());
        $this->_resource->setDeleted(true);
    }
    
    public function getContents()
    {
        return null;
    }
    
}