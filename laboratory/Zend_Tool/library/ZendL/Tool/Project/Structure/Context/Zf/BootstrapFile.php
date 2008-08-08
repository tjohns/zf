<?php

class ZendL_Tool_Project_Structure_Context_Zf_BootstrapFile extends ZendL_Tool_Project_Structure_Context_Filesystem_File 
{
    
    protected $_filesystemName = 'bootstrap.php';
    
    public function getName()
    {
        return 'BootstrapFile';
    }
    
}