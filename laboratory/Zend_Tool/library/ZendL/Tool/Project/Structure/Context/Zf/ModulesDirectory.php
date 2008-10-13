<?php

class ZendL_Tool_Project_Structure_Context_Zf_ModulesDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'modules';
    
    public function getName()
    {
        return 'ModulesDirectory';
    }
    
}