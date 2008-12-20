<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

class Zend_Tool_Project_Context_System_ProjectProfileFile extends Zend_Tool_Project_Context_Filesystem_File 
{

    protected $_filesystemName = '.zfproject.xml';
    
    protected $_profile = null;
    
    public function getName()
    {
        return 'ProjectProfileFile';
    }
    
    public function setProfile($profile)
    {
        $this->_profile = $profile;
    }
    
    public function getContents()
    {
        $parser = new Zend_Tool_Project_ProfileFileParser_Xml();
        $isTraverseEnabled = Zend_Tool_Project_Profile::isTraverseEnabled();
        Zend_Tool_Project_Profile::setTraverseEnabled(true);
        $xml = $parser->serialize($this->_profile);
        Zend_Tool_Project_Profile::setTraverseEnabled($isTraverseEnabled);
        return $xml;
    }
    
}