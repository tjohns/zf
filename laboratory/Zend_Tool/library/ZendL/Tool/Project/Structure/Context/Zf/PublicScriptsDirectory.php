<?php

class ZendL_Tool_Project_Structure_Context_Zf_PublicScriptsDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'js';
    
    public function getName()
    {
        return 'PublicScriptsDirectory';
    }
    
}