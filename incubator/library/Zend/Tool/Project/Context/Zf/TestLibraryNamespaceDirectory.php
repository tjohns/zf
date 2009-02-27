<?php

class Zend_Tool_Project_Context_Zf_TestLibraryNamespaceDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_namespaceName  = '';
    protected $_filesystemName = 'library';

    public function getName()
    {
        return 'TestLibraryNamespaceDirectory';
    }
    
    public function init()
    {
        $this->_namespaceName  = $this->_resource->getAttribute('namespaceName');
        $this->_filesystemName = $this->_namespaceName;
        parent::init();
    }
    
    public function getPersistentAttributes()
    {
        $attributes = array();
        $attributes['namespaceName'] = $this->_namespaceName;      
   
        return $attributes;
    }
    
}
