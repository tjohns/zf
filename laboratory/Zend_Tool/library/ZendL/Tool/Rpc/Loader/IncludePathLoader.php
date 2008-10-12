<?php

require_once 'ZendL/Tool/Rpc/Loader/IncludePathLoader/RecursiveFilterIterator.php';

class ZendL_Tool_Rpc_Loader_IncludePathLoader extends ZendL_Tool_Rpc_Loader_Abstract 
{

    protected $_filterDenyDirectoryPattern = '.*(/|\\\\).svn';
    protected $_filterAcceptFilePattern = '.*(?:Tool|Manifest|Provider)\.php';
    
    protected function _getFiles()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        $files = array();
        
        ZendL_Tool_Rpc_Loader_IncludePathLoader_RecursiveFilterIterator::setDenyDirectoryPattern($this->_filterDenyDirectoryPattern);
        ZendL_Tool_Rpc_Loader_IncludePathLoader_RecursiveFilterIterator::setAcceptFilePattern($this->_filterAcceptFilePattern);
        
        foreach ($paths as $path) {
        	
            if (!file_exists($path) || $path[0] == '.') {
                continue;
            }
            
            $filter = new ZendL_Tool_Rpc_Loader_IncludePathLoader_RecursiveFilterIterator(new RecursiveDirectoryIterator($path));
            
            $iterator = new RecursiveIteratorIterator($filter);

            foreach ($iterator as $item) {
                if (!$item->isLink()) {
                    $files[] = $item->getRealPath();
                }
            }
        }

        return $files;
    }
    
}
