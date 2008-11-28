<?php

class Zend_Tool_Project_Structure_Context_Zf_SessionsDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'sessions';
    
    public function getName()
    {
        return 'SessionsDirectory';
    }
    
}