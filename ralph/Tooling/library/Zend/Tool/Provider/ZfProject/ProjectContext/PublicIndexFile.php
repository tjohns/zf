<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_PublicIndexFile extends Zend_Tool_Provider_ZfProject_ProjectContext_File 
{
    
    protected $_name = 'index.php';
    
    public function getContextName()
    {
        return 'publicIndexFile';
    }
    
}