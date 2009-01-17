<?php

class Zend_Tool_Project_Context_Zf_CacheDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'cache';
    
    public function getName()
    {
        return 'CacheDirectory';
    }
    
}