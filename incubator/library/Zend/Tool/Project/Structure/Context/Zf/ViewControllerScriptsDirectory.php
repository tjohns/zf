<?php

class Zend_Tool_Project_Structure_Context_Zf_ViewControllerScriptsDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'controllerName';

    protected $_forControllerName = null;
    
    public function getPersistentParameters()
    {
        return array(
            'forControllerName' => $this->_forControllerName
            );
    }
    
    public function getName()
    {
        return 'ViewControllerScriptsDirectory';
    }
    
    public function setForControllerName($forControllerName)
    {
        $this->_filesystemName = $forControllerName;
        $this->_forControllerName = $forControllerName;
    }
    
}