<?php

class Zend_Tool_Project_Context_Zf_ViewsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory
{
    protected $_filesystemName = 'views';
    
    public function getName()
    {
        return 'ViewsDirectory';
    }
    
}