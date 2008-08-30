<?php

class ZendL_Tool_Project_Structure_Context_Zf_SearchIndexesDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'search-indexes';
    
    public function getName()
    {
        return 'SearchIndexesDirectory';
    }
    
}