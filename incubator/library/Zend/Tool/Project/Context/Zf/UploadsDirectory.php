<?php

class Zend_Tool_Project_Context_Zf_UploadsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'uploads';
    
    public function getName()
    {
        return 'UploadsDirectory';
    }
    
}