<?php

class Zend_Tool_Project_Structure_Context_Zf_ProjectDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = null;
    
    public function getName()
    {
        return 'ProjectDirectory';
    }
    
}