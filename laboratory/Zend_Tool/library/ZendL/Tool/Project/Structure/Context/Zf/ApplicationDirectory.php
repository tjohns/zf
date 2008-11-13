<?php

class ZendL_Tool_Project_Structure_Context_Zf_ApplicationDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'application';
    
    public function getName()
    {
        return 'ApplicationDirectory';
    }
    
}