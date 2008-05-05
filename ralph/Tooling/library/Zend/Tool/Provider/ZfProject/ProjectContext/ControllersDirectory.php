<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ControllersDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    
    protected $_name = 'controllers';
    
    public function getContextName()
    {
        return 'controllersDirectory';
    }
    
}