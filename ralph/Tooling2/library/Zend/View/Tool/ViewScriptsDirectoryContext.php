<?php

class Zend_View_Tool_ViewScriptsDirectoryContext extends Zend_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'scripts';
    
    public function getName()
    {
        return 'ViewScriptsDirectory';
    }
    
}