<?php

class Zend_Tool_Project_Context_Zf_TestsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'tests';
    
    public function getName()
    {
        return 'TestsDirectory';
    }
    
}