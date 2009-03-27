<?php

class Zend_Tool_Project_Context_Zf_TestApplicationBootstrapFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    protected $_filesystemName = 'bootstrap.php';
    
    public function getName()
    {
        return 'TestApplicationBootstrapFile';
    }
    
}