<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ModulesDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    
    protected $_name = 'modules';
    
    public function getContextName()
    {
        return 'modulesDirectory';
    }
    
}