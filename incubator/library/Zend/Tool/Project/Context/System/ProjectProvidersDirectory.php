<?php

require_once 'Zend/Tool/Project/Context/Filesystem/Directory.php';
require_once 'Zend/Tool/Project/Context/System/ISystem.php';
require_once 'Zend/Tool/Project/Context/System/INotOverwritable.php';

class Zend_Tool_Project_Context_System_ProjectProvidersDirectory 
    extends Zend_Tool_Project_Context_Filesystem_Directory
    implements Zend_Tool_Project_Context_System_ISystem,
               Zend_Tool_Project_Context_System_INotOverwritable
{
    
    protected $_filesystemName = 'providers';
    
    public function getName()
    {
        return 'ProvidersDirectory';
    }
    
    public function init()
    {
        parent::init();
        
        if (file_exists($this->getPath())) {
            // @todo
            echo 'Loop through here and find the appropriate providers for lazy loading';
        }
        
    }
    
}