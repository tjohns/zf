<?php

class Zend_View_Tool_ViewsDirectoryContext extends Zend_Tool_Project_Structure_Context_Filesystem_Directory
{
    protected $_filesystemName = 'views';
    
    public function getName()
    {
        return 'ViewsDirectory';
    }
    
}