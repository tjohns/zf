<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ViewControllerScriptsDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'index';
    
    public function getContextName()
    {
        return 'viewControllerScriptsDirectory';
    }
    
}