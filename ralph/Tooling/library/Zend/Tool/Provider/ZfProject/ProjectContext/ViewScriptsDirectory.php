<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ViewScriptsDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    
    protected $_name = 'scripts';
    
    public function getContextName()
    {
        return 'viewScriptsDirectory';
    }
    
}