<?php

class Zend_Tool_Project_Structure_Context_Zf_PublicIndexFile extends Zend_Tool_Project_Structure_Context_Filesystem_File 
{
    
    protected $_filesystemName = 'index.php';
    
    public function getName()
    {
        return 'PublicIndexFile';
    }
    
}