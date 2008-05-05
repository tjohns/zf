<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ViewsDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    
    protected $_name = 'views';
    
    public function getContextName()
    {
        return 'viewsDirectory';
    }
    
}