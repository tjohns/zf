<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_ViewFiltersDirectory extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_Directory 
{
    
    protected $_name = 'filters';
    
    public function getContextName()
    {
        return 'viewFiltersDirectory';
    }
    
}