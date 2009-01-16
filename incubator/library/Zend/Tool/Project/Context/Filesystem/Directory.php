<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Abstract.php';

abstract class Zend_Tool_Project_Context_Filesystem_Directory extends Zend_Tool_Project_Context_Filesystem_Abstract 
{
    
    /**
     * @todo Determine if this is needed
     *
     */
//    public function setDirectoryName($directoryName)
//    {
//        $this->_baseDirectory = rtrim($directoryName, '\\/');
//    }

    public function create()
    {
        // @todo do more to check the sanity here
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