<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ApplicationDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    
    protected $_name = 'application';
    
    public function getContextName()
    {
        return 'applicationDirectory';
    }
    
}