<?php

class Zend_Tool_Project_Context_Zf_ApplicationConfigFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    protected $_filesystemName = 'application.ini';
    
    public function getName()
    {
        return 'ApplicationConfigFile';
    }
    
    public function init()
    {
        $this->_type = $this->_resource->getAttribute('type');
        parent::init();
    }
    
    public function getPersistentAttributes()
    {
        return array('type' => $this->_type);
    }
    
    public function getContents()
    {
        $contents = '';
        $contents .= '[production]'
            . PHP_EOL . PHP_EOL
            . '[staging : production]'
            . PHP_EOL . PHP_EOL
            . '[development : production]';
        return $contents;
    }
    
}