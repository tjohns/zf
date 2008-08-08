<?php

class ZendL_View_Tool_ViewHelpersDirectoryContext extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'helpers';
    
    public function getName()
    {
        return 'ViewHelpersDirectory';
    }
    
}