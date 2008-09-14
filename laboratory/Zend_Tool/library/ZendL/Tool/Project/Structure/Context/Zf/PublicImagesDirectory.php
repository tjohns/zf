<?php

class ZendL_Tool_Project_Structure_Context_Zf_PublicImagesDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'images';
    
    public function getName()
    {
        return 'PublicImagesDirectory';
    }
    
}