<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ControllersDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'controllers';
    
    public function getContextName()
    {
        return 'controllersDirectory';
    }
    
}