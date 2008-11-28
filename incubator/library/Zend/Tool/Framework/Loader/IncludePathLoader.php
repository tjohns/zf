<?php

require_once 'Zend/Tool/Framework/Loader/IncludePathLoader/RecursiveFilterIterator.php';

class Zend_Tool_Framework_Loader_IncludePathLoader extends Zend_Tool_Framework_Loader_Abstract 
{

    protected $_filterDenyDirectoryPattern = '.*(/|\\\\).svn';
    protected $_filterAcceptFilePattern = '.*(?:Manifest|Provider)\.php$';
    
    protected function _getFiles()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        $files = array();
        
        Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator::setDenyDirectoryPattern($this->_filterDenyDirectoryPattern);
        Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator::setAcceptFilePattern($this->_filterAcceptFilePattern);
        
        foreach ($paths as $path) {
        	
            if (!file_exists($path) || $path[0] == '.') {
                continue;
            }
            
            $filter = new Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator(new RecursiveDirectoryIterator($path));
            
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
