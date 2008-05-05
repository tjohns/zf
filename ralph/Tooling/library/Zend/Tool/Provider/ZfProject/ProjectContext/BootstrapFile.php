<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_BootstrapFile extends Zend_Tool_Provider_ZfProject_ProjectContext_File
{
    
    protected $_name = 'bootstrap.php';
    
    public function getContextName()
    {
        return 'bootstrapFile';
    }
    
    public function append(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $node)
    {
        throw new Exception('cannot append to a file');
    }
    
}