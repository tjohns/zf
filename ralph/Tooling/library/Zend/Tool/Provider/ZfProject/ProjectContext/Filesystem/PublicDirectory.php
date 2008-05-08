<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_PublicDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    protected $_name = 'public';
    
    public function getContextName()
    {
        return 'publicDirectory';
    }
    
}