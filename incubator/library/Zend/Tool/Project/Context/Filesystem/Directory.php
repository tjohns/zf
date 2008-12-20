<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Abstract.php';

abstract class Zend_Tool_Project_Context_Filesystem_Directory extends Zend_Tool_Project_Context_Filesystem_Abstract 
{
    
    public function setDirectoryName($directoryName) {
        $this->_filesystemName = $directoryName;
    }

    public function create()
    {

        // @todo do more to check the sanity here
        if (!file_exists($this->getPath())) {
            mkdir($this->getPath());
        }

    }
    
    public function recursivelyCreate()
    {
        $this->create();
        foreach ($this->_resource as $subResource) {
            $subNode->recursivelyCreate();
        }
    }
    
    public function delete()
    {
        $this->_resource->setDeleted(true);
        rmdir($this->getPath());        
    }
    
    public function recursivelyDelete()
    {
        if ($this->_recurseSubContexts) {
            foreach ($this->_resource as $subResource) {
                $subResource->delete();
            }
        }
        
    }
    
}