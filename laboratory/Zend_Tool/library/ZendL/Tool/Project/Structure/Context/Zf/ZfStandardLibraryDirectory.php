<?php

class ZendL_Tool_Project_Structure_Context_Zf_ZfStandardLibraryDirectory extends ZendL_Tool_Project_Structure_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'Zend';
    
    public function getName()
    {
        return 'ZfStandardLibraryDirectory';
    }
    
    public function create()
    {
        parent::create();
        $zfPath = $this->_getZfPath();
        if ($zfPath != false) {
            $zfIterator = new RecursiveDirectoryIterator($zfPath);
            foreach ($rii = new RecursiveIteratorIterator($zfIterator, RecursiveIteratorIterator::SELF_FIRST) as $file) {
                $relativePath = preg_replace('#^'.preg_quote(realpath($zfPath), '#').'#', '', realpath($file->getPath())) . DIRECTORY_SEPARATOR . $file->getFilename();
                if (strpos($relativePath, DIRECTORY_SEPARATOR . '.') !== false) {
                    continue;
                }
                
                if ($file->isDir()) {
                    mkdir($this->getBaseDirectory() . DIRECTORY_SEPARATOR . $this->getFilesystemName() . $relativePath);
                } else {
                    copy($file->getPathname(), $this->getBaseDirectory() . DIRECTORY_SEPARATOR . $this->getFilesystemName() . $relativePath);
                }
                    
            }
        }
    }
    
    protected function _getZfPath()
    {
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $includePath) {
        	
            if (!file_exists($includePath) || $includePath[0] == '.') {
                continue;
            }
        	
            if (realpath($checkedPath = rtrim($includePath, '\\/') . '/Zend/Loader.php') !== false && file_exists($checkedPath)) {
                return dirname($checkedPath);
            }
        }
        
        return false;
    }
    
}