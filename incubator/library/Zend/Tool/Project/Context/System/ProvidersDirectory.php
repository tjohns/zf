<?php

class Zend_Tool_Project_Context_System_ProvidersDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'providers';
    
    public function getName()
    {
        return 'ProvidersDirectory';
    }
    
}