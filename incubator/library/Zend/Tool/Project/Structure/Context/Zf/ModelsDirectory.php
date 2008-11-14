<?php

class Zend_Tool_Project_Structure_Context_Zf_ModelsDirectory extends Zend_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'models';
    
    public function getName()
    {
        return 'ModelsDirectory';
    }
    
}