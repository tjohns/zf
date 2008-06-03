<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ViewScriptFile extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_File 
{
    
    protected $_name = 'index';
    
    public function getContextName()
    {
        return 'viewScriptFile';
    }
    
    public function getFileName()
    {
        return $this->getBaseDirectoryName() . '/' . $this->getName() . '.phtml';
    }
    
}