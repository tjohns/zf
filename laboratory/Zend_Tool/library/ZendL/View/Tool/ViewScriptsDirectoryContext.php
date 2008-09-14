<?php

class ZendL_View_Tool_ViewScriptsDirectoryContext extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'scripts';
    
    public function getName()
    {
        return 'ViewScriptsDirectory';
    }
    
}