<?php

class Zend_Tool_Project_Structure_Context_Zf_ApisDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'apis';
    
    public function getName()
    {
        return 'ApisDirectory';
    }
    
}