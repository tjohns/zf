<?php

class Zend_Tool_Project_Context_Zf_PublicDirectory extends Zend_Tool_Project_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'public';
    
    public function getName()
    {
        return 'PublicDirectory';
    }
    
}