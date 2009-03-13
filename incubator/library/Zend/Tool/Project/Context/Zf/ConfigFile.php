<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

class Zend_Tool_Project_Context_Zf_ConfigFile extends Zend_Tool_Project_Context_Filesystem_File 
{
    
    protected $_filesystemName = 'bootstrap.php';
    
    public function getName()
    {
        return 'ConfigFile';
    }
    
    public function getContents()
    {
        return '';
    }
    
}