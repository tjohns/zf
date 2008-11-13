<?php

class ZendL_Tool_Project_Structure_Context_Zf_ConfigsDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'configs';
    
    public function getName()
    {
        return 'ConfigsDirectory';
    }
    
}