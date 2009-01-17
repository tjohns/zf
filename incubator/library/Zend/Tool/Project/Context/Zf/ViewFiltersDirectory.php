<?php

class Zend_Tool_Project_Context_Zf_ViewFiltersDirectory extends Zend_Tool_Project_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'filters';
    
    public function getName()
    {
        return 'ViewFiltersDirectory';
    }
    
}