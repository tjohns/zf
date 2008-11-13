<?php

class ZendL_Tool_Project_Structure_Context_Zf_ViewScriptsDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'scripts';
    
    public function getName()
    {
        return 'ViewScriptsDirectory';
    }
    
}