<?php

class Zend_Tool_Project_Structure_Context_Zf_UploadsDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'uploads';
    
    public function getName()
    {
        return 'UploadsDirectory';
    }
    
}