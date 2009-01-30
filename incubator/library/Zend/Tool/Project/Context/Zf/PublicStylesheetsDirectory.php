<?php

class Zend_Tool_Project_Context_Zf_PublicStylesheetsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'styles';
    
    public function getName()
    {
        return 'PublicStylesheetsDirectory';
    }
    
}