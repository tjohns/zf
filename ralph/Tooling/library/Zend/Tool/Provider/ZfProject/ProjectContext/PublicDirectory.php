<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_PublicDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    protected $_name = 'public';
    
    public function getContextName()
    {
        return 'publicDirectory';
    }
    
}