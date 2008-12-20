<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Directory.php';

class Zend_Tool_Project_Context_System_ProjectDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = null;
    
    public function getName()
    {
        return 'ProjectDirectory';
    }
    
}