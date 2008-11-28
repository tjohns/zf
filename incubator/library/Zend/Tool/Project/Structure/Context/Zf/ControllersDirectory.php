<?php

require_once 'Zend/Tool/Project/Structure/Context/Filesystem/Directory.php';

class Zend_Tool_Project_Structure_Context_Zf_ControllersDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'controllers';
    
    public function getName()
    {
        return 'ControllersDirectory';
    }
    
}