<?php

class ZendL_Tool_Project_Structure_Context_Zf_ZfStandardLibraryDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'Zend';
    
    public function getName()
    {
        return 'ZfStandardLibraryDirectory';
    }
    
}