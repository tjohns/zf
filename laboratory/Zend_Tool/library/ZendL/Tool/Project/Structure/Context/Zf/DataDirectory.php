<?php

class ZendL_Tool_Project_Structure_Context_Zf_DataDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'data';
    
    public function getName()
    {
        return 'DataDirectory';
    }
    
}