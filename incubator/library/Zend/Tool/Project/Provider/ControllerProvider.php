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
        
        $structureGraph = $this->_getExistingStructureGraph();

        $controllersDirectoryNode = $structureGraph->findNodeByContext('controllersDirectory');
        
        $controllerFileContext = Zend_Tool_Project_Structure_Context_Registry::getInstance()->getContext('controllerFile');
        $newNode = new Zend_Tool_Project_Structure_Node($controllerFileContext);
        $newNode->setBaseDirectory($controllersDirectoryNode->getContext()->getPath());
        $newNode->setControllerName($name);
        
        
        echo 'Creating new controller named \'' . $name . '\'' . PHP_EOL;
        $newNode->create();
        $controllersDirectoryNode->append($newNode);
        $this->_storeLoadedStructureGraph();
        
        if ($viewincluded) {
            $viewProvider = Zend_Tool_Framework_Provider_Registry::getInstance()->getProvider('View');
            $viewProvider->create($name, 'index');
        }
        
    }
    
}