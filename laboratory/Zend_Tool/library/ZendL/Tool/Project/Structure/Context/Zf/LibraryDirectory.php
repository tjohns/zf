<?php

class ZendL_Tool_Project_Structure_Context_Zf_LibraryDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'library';
    
    public function getName()
    {
        return 'LibraryDirectory';
    }
    
}