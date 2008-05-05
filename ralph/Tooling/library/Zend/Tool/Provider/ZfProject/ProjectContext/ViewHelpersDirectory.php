<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ViewHelpersDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    
    protected $_name = 'helpers';
    
    public function getContextName()
    {
        return 'viewHelpersDirectory';
    }
    
}