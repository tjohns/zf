<?php

class Zend_Tool_Project_Structure_Context_Zf_PublicDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'public';
    
    public function getName()
    {
        return 'PublicDirectory';
    }
    
}