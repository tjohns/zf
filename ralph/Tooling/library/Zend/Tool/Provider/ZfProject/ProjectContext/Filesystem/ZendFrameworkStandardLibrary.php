<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ZendFrameworkStandardLibrary extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'Zend';
    
    public function getContextName()
    {
        return 'zendFrameworkStandardLibrary';
    }
    
}