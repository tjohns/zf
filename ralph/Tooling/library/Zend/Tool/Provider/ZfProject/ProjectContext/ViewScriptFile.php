<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_ViewScriptFile extends Zend_Tool_Provider_ZfProject_ProjectContext_File 
{
    
    protected $_name = 'view-script.php';
    
    public function getContextName()
    {
        return 'viewScriptFile';
    }
    
}