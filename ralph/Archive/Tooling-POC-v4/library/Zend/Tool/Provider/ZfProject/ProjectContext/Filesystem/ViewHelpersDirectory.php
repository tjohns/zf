<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ViewHelpersDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'helpers';
    
    public function getContextName()
    {
        return 'viewHelpersDirectory';
    }
    
}