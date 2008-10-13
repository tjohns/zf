<?php

class ZendL_Tool_Project_Structure_Context_Zf_ProjectDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = null;
    
    public function getName()
    {
        return 'ProjectDirectory';
    }
    
}