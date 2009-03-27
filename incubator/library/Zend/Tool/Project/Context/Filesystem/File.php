<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Abstract.php';

abstract class Zend_Tool_Project_Context_Filesystem_File extends Zend_Tool_Project_Context_Filesystem_Abstract 
{
    
    public function init()
    {
        // @todo check to ensure that this 'file' resource has no children
        parent::init();
    }
    
    public function setResource($resource)
    {
        $this->_resource = $resource;
        $this->_resource->setAppendable(false);
    }

    public function create()
    {
        // check to ensure the parent exists, if not, call it and create it
        if (($parentResource = $this->_resource->getParentResource()) instanceof Zend_Tool_Project_Profile_Resource) {
            if ((($parentContext = $parentResource->getContext()) instanceof Zend_Tool_Project_Context_Filesystem_Abstract)
                && (!$parentContext->exists())) {
                $parentResource->create();
            }
        }
        
        
        if (file_exists($this->getPath())) {
            //if (Zend_Tool_Framework_Registry::getInstance()->getClient()->isInteractive()) {
                // @todo prompt user
            //} else {
                
            //}
        }
        
        file_put_contents($this->getPath(), $this->getContents());
        return $this;
    }
    
    public function delete()
    {
        unlink($this->getPath());
        $this->_resource->setDeleted(true);
        return $this;
    }
    
    public function getContents()
    {
        return null;
    }
    
}