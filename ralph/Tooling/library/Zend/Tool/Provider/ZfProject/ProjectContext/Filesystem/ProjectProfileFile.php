<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ProjectProfileFile extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_File 
{

    protected $_projectProfile = null;
    
    protected $_name = '.zfproject.xml';
    
    protected $_persistentParameters = array();

    public function setProjectProfile(Zend_Tool_Provider_ZfProject_ProjectProfile $projectProfile)
    {
        $this->_projectProfile = $projectProfile;
        return $this;
    }
    
    public function getContents()
    {
        return $this->_projectProfile->toString();
    }

    public function refresh()
    {
        $this->create();
    }
}
