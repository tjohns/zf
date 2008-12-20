<?php

class Zend_Tool_Project_Context_Zf_PublicImagesDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'images';
    
    public function getName()
    {
        return 'PublicImagesDirectory';
    }
    
}