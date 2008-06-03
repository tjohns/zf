<?php

class Zend_Tool_Project_Structure_Context_Zf_HtaccessFile extends Zend_Tool_Project_Structure_Context_Filesystem_File 
{
    
    protected $_filesystemName = '.htaccess';
    
    public function getName()
    {
        return 'HtaccessFile';
    }
    
}