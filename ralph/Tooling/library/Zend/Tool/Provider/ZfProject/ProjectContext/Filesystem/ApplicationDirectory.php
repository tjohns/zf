<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ApplicationDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'application';
    
    public function getContextName()
    {
        return 'applicationDirectory';
    }
    
}