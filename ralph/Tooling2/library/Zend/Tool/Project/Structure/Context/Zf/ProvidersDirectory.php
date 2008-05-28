<?php

class Zend_Tool_Project_Structure_Context_Zf_ProvidersDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'providers';
    
    public function getName()
    {
        return 'ProvidersDirectory';
    }
    
}