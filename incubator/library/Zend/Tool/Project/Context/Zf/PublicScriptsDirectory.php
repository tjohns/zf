<?php

class Zend_Tool_Project_Context_Zf_PublicScriptsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'js';
    
    public function getName()
    {
        return 'PublicScriptsDirectory';
    }
    
}