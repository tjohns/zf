<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ProvidersDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'providers';
    
    public function getContextName()
    {
        return 'providersDirectory';
    }
    
}