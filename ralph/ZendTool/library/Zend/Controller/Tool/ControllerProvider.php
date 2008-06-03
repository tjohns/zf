<?php

class Zend_Controller_Tool_ControllerProvider extends Zend_Tool_Project_Provider_Abstract 
{
    
    public function getContextClasses()
    {
        return array(
            'Zend_Controller_Tool_ControllerFileContext',
            'Zend_Controller_Tool_ControllersDirectoryContext'
            );
        
    }
    
    public function create($name)
    {
        $structureGraph = $this->_loadExistingStructureGraph();

        $controllersDirectory = $structureGraph->findNodeByContext('controllersDirectory');
        
        $controllerFileContext = Zend_Tool_Project_Structure_Context_Registry::getInstance()->getContext('controllerFile');
        $newNode = new Zend_Tool_Project_Structure_Node($controllerFileContext);
        $newNode->setBaseDirectory($controllersDirectory->getContext()->getPath());
        $newNode->setControllerName($name);
        
        echo 'Creating new controller named \'' . $name . '\'' . PHP_EOL;
        $newNode->create();
        
        $controllersDirectory->append($newNode);

        $structureGraph->storeLoadedStructureGraph();
    }
    
}