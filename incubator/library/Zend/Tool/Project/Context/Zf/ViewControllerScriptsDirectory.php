<?php

class Zend_Tool_Project_Context_Zf_ViewControllerScriptsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'controllerName';

    protected $_forControllerName = null;
    
    public function init()
    {
        $this->_forControllerName = $this->_resource->getAttribute('forControllerName');
        $this->_filesystemName = $this->_forControllerName;
        
        parent::init();
    }
    
    public function getPersistentAttributes()
    {
        return array(
            'forControllerName' => $this->_forControllerName
            );
    }
    
    public function getName()
    {
        return 'ViewControllerScriptsDirectory';
    }
    
}