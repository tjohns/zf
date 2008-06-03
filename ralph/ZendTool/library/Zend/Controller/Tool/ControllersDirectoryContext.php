<?php

class Zend_Controller_Tool_ControllersDirectoryContext extends Zend_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'controllers';
    
    public function getName()
    {
        return 'ControllersDirectory';
    }
    
}