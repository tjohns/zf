<?php

class Zend_Tool_Project_Context_Zf_LayoutsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'layouts';
    
    public function getName()
    {
        return 'LayoutsDirectory';
    }
    
}