<?php

class ZendL_Tool_Project_Structure_Context_Zf_CacheDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'cache';
    
    public function getName()
    {
        return 'CacheDirectory';
    }
    
}