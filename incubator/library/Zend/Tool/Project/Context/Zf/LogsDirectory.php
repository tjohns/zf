<?php

class Zend_Tool_Project_Context_Zf_LogsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'logs';
    
    public function getName()
    {
        return 'LogsDirectory';
    }
    
}