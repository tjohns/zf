<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Directory.php';

class Zend_Tool_Project_Context_Zf_TemporaryDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'temp';
    
    public function getName()
    {
        return 'TemporaryDirectory';
    }
    
}