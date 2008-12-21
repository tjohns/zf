<?php

class Zend_Tool_Project_Context_Zf_ViewHelpersDirectory extends Zend_Tool_Project_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'helpers';
    
    public function getName()
    {
        return 'ViewHelpersDirectory';
    }
    
}