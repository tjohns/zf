<?php

class ZendL_Tool_Project_Structure_Context_Zf_PublicStylesheetsDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'styles';
    
    public function getName()
    {
        return 'PublicStylesheetsDirectory';
    }
    
}