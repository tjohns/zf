<?php

class ZendL_Tool_Project_Structure_Context_Zf_TestsDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'tests';
    
    public function getName()
    {
        return 'TestsDirectory';
    }
    
}