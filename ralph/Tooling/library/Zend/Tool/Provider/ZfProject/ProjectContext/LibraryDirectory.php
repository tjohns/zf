<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_LibraryDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Directory 
{
    
    protected $_name = 'library';
    
    public function getContextName()
    {
        return 'libraryDirectory';
    }
    
}