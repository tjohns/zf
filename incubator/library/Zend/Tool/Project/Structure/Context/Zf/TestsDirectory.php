<?php

class Zend_Tool_Project_Structure_Context_Zf_TestsDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'tests';
    
    public function getName()
    {
        return 'TestsDirectory';
    }
    
}