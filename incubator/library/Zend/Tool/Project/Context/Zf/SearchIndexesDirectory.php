<?php

class Zend_Tool_Project_Context_Zf_SearchIndexesDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'search-indexes';
    
    public function getName()
    {
        return 'SearchIndexesDirectory';
    }
    
}