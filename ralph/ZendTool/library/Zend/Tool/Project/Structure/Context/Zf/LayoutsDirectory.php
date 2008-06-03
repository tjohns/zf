<?php

class Zend_Tool_Project_Structure_Context_Zf_LayoutsDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'layouts';
    
    public function getName()
    {
        return 'LayoutsDirectory';
    }
    
}