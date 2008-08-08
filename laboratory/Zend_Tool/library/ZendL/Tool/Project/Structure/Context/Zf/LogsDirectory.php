<?php

class ZendL_Tool_Project_Structure_Context_Zf_LogsDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'logs';
    
    public function getName()
    {
        return 'LogsDirectory';
    }
    
}