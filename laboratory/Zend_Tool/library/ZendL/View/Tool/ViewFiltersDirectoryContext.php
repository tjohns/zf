<?php

class ZendL_View_Tool_ViewFiltersDirectoryContext extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory
{
    
    protected $_filesystemName = 'filters';
    
    public function getName()
    {
        return 'ViewFiltersDirectory';
    }
    
}