<?php

require_once 'ZendL/Tool/Project/Structure/Context/Filesystem/Directory.php';

class ZendL_Controller_Tool_ControllersDirectoryContext extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'controllers';
    
    public function getName()
    {
        return 'ControllersDirectory';
    }
    
}