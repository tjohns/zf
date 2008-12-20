<?php

class Zend_Tool_Project_Context_Zf_LocalesDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'locales';
    
    public function getName()
    {
        return 'LocalesDirectory';
    }
    
}