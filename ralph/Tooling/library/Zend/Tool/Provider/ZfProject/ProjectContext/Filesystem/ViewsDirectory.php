<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ViewsDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'views';
    
    public function getContextName()
    {
        return 'viewsDirectory';
    }
    
}