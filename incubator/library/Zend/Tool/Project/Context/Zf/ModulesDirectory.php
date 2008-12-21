<?php

class Zend_Tool_Project_Context_Zf_ModulesDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'modules';
    
    public function getName()
    {
        return 'ModulesDirectory';
    }
    
}