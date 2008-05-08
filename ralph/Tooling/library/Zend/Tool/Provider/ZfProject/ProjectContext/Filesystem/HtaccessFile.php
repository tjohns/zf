<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_HtaccessFile extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_File 
{
    protected $_name = '.htaccess';
    
    public function getContextName()
    {
        return 'htaccessFile';
    }
    
}