<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Directory.php';
require_once 'Zend/Tool/Project/Context/System/Interface.php';
require_once 'Zend/Tool/Project/Context/System/NotOverwritable.php';

class Zend_Tool_Project_Context_System_ProjectProvidersDirectory 
    extends Zend_Tool_Project_Context_Filesystem_Directory
    implements Zend_Tool_Project_Context_System_Interface,
               Zend_Tool_Project_Context_System_NotOverwritable
{
    
    protected $_filesystemName = 'providers';
    
    public function getName()
    {
        return 'ProjectProvidersDirectory';
    }
    
    public function init()
    {
        parent::init();
        
        if (file_exists($this->getPath())) {

            foreach (new DirectoryIterator($this->getPath()) as $item) {
                if ($item->isFile()) {
                    $loadableFiles[] = $item->getPathname();
                }
            }
            
            if ($loadableFiles) {
                $loader = Zend_Tool_Framework_Registry::getInstance()->getLoader();
                $loader->loadFromFiles($loadableFiles);
            }
        }
        
    }
    
}