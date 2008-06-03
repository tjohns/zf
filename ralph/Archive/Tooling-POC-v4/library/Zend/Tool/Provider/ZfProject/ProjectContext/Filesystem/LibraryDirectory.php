<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_LibraryDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'library';
    
    public function getContextName()
    {
        return 'libraryDirectory';
    }
    
}