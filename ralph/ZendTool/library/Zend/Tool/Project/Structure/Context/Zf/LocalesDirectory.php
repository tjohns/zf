<?php

class Zend_Tool_Project_Structure_Context_Zf_LocalesDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'locales';
    
    public function getName()
    {
        return 'LocalesDirectory';
    }
    
}