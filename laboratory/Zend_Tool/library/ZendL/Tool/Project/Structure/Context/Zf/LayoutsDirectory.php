<?php

class ZendL_Tool_Project_Structure_Context_Zf_LayoutsDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'layouts';
    
    public function getName()
    {
        return 'LayoutsDirectory';
    }
    
}