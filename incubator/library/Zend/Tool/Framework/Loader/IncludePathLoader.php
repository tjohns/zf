<?php

require_once 'Zend/Tool/Framework/Loader/Abstract.php';
require_once 'Zend/Tool/Framework/Loader/IncludePathLoader/RecursiveFilterIterator.php';


class Zend_Tool_Framework_Loader_IncludePathLoader extends Zend_Tool_Framework_Loader_Abstract 
{

    protected $_filterDenyDirectoryPattern = '.*(/|\\\\).svn';
    protected $_filterAcceptFilePattern = '.*(?:Manifest|Provider)\.php$';
    
    protected function _getFiles()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        // used for checking similarly named files
        $relativeItems = array();
        $files = array();
        
        Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator::setDenyDirectoryPattern($this->_filterDenyDirectoryPattern);
        Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator::setAcceptFilePattern($this->_filterAcceptFilePattern);
        
        foreach ($paths as $path) {
        	
            if (!file_exists($path) || $path[0] == '.') {
                continue;
            }
            
            $realIncludePath = realpath($path);
            
            $filter = new Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator(new RecursiveDirectoryIterator($path));
            
            $iterator = new RecursiveIteratorIterator($filter);

            foreach ($iterator as $item) {
                // ensure that the same named file from separate include_paths is not loaded
                $relativeItem = preg_replace('#^' . $realIncludePath . DIRECTORY_SEPARATOR . '#', '', $item->getRealPath());
                
                if (!$item->isLink() && !in_array($relativeItem, $relativeItems)) {
                    $relativeItems[] = $relativeItem;
                    $files[] = $item->getRealPath();
                }
            }
        }
        
        return $files;
    }
    
}
