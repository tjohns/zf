<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ViewScriptsDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'scripts';
    
    public function getContextName()
    {
        return 'viewScriptsDirectory';
    }
    
}