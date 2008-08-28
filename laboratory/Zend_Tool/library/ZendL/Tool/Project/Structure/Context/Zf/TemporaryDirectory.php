<?php

require_once 'ZendL/Tool/Project/Structure/Context/Filesystem/Directory.php';

class ZendL_Tool_Project_Structure_Context_Zf_TemporaryDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'temp';
    
    public function getName()
    {
        return 'TemporaryDirectory';
    }
    
}