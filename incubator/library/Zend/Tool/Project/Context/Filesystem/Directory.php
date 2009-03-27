<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Abstract.php';

abstract class Zend_Tool_Project_Context_Filesystem_Directory extends Zend_Tool_Project_Context_Filesystem_Abstract 
{
    
    public function create()
    {
        // check to ensure the parent exists, if not, call it and create it
        if (($parentResource = $this->_resource->getParentResource()) instanceof Zend_Tool_Project_Profile_Resource) {
            if ((($parentContext = $parentResource->getContext()) instanceof Zend_Tool_Project_Context_Filesystem_Abstract)
                && (!$parentContext->exists())) {
                $parentResource->create();
            }
        }
        
        if (!file_exists($this->getPath())) {
            mkdir($this->getPath());
        }

    }
    
    public function delete()
    {
        $this->_resource->setDeleted(true);
        rmdir($this->getPath());        
    }
    
}