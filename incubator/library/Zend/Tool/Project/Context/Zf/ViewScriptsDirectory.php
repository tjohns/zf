<?php

class Zend_Tool_Project_Context_Zf_ViewScriptsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'scripts';
    
    public function getName()
    {
        return 'ViewScriptsDirectory';
    }
    
}