<?php

require_once 'Zend/Tool/Rpc/Loader/IncludePathLoader/RecursiveFilterIterator.php';

class Zend_Tool_Rpc_Loader_IncludePathLoader extends Zend_Tool_Rpc_Loader_Abstract 
{

    protected $_filterDenyDirectoryPattern = '.*/.svn';
    protected $_filterAcceptFilePattern = '.*(?:Tool|Manifest|Provider)\.php';
    
    protected function _getFiles()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        
        $files = array();
        
        Zend_Tool_Rpc_Loader_IncludePathLoader_RecursiveFilterIterator::setDenyDirectoryPattern($this->_filterDenyDirectoryPattern);
        Zend_Tool_Rpc_Loader_IncludePathLoader_RecursiveFilterIterator::setAcceptFilePattern($this->_filterAcceptFilePattern);
        
        foreach ($paths as $path) {

            $filter = new Zend_Tool_Rpc_Loader_IncludePathLoader_RecursiveFilterIterator(new RecursiveDirectoryIterator($path));
            
            $iterator = new RecursiveIteratorIterator($filter);

            foreach ($iterator as $item) {
                $files[] = $item->getRealPath();
            }
        }
        
        return $files;
    }
    

    
}
