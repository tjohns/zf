<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_PublicIndexFile extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_File 
{
    
    protected $_name = 'index.php';
    
    public function getContextName()
    {
        return 'publicIndexFile';
    }
    
    public function getContents()
    {

        $profileSet = $this->getProfileSet();

        $output = $profileSet->publicIndexFile();
        return $output;
    }
    
}