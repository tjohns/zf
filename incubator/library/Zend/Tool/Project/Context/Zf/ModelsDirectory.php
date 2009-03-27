<?php

class Zend_Tool_Project_Context_Zf_ModelsDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'models';
    
    public function getName()
    {
        return 'ModelsDirectory';
    }
    
}