<?php

class Zend_Tool_Project_Structure_Context_Zf_ZfStandardLibraryDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'Zend';
    
    public function getName()
    {
        return 'ZfStandardLibraryDirectory';
    }
    
}