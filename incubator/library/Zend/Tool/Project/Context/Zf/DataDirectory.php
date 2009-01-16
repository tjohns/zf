<?php

class Zend_Tool_Project_Context_Zf_DataDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'data';
    
    public function getName()
    {
        return 'DataDirectory';
    }
    
}