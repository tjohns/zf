<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';

class Zend_Tool_Project_Provider_Controller extends Zend_Tool_Project_Provider_Abstract 
{
    
    /*
    public function getContextClasses()
    {
        return array(
            'Zend_Controller_Tool_ControllerFileContext', // Context: ControllerFile
            'Zend_Controller_Tool_ControllersDirectoryContext' // Context: ControllersDirectory
            );
    }
    */
    
    public function create($name, $viewincluded = true)
    {
        
        $profile = $this->_getExistingProfile();

        $controllersDirectoryNode = $profile->findNodeByContext('controllersDirectory');
        
        $controllerFileContext = Zend_Tool_Project_Context_Registry::getInstance()->getContext('controllerFile');
        $newResource = new Zend_Tool_Project_Resource($controllerFileContext);
        $newResource->setBaseDirectory($controllersDirectoryNode->getContext()->getPath());
        $newResource->setControllerName($name);
        
        
        echo 'Creating new controller named \'' . $name . '\'' . PHP_EOL;
        $newResource->create();
        $controllersDirectoryNode->append($newResource);
        $this->_storeLoadedProfile();
        
        if ($viewincluded) {
            $viewProvider = Zend_Tool_Framework_Provider_Registry::getInstance()->getProvider('View');
            $viewProvider->create($name, 'index');
        }
        
    }
    
}