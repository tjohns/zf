<?php

require_once 'ZendL/Tool/Project/Provider/Abstract.php';

class ZendL_Controller_Tool_ControllerProvider extends ZendL_Tool_Project_Provider_Abstract 
{
    
    public function getContextClasses()
    {
        return array(
            'ZendL_Controller_Tool_ControllerFileContext', // Context: ControllerFile
            'ZendL_Controller_Tool_ControllersDirectoryContext' // Context: ControllersDirectory
            );
    }
    
    public function create($name, $viewincluded = true)
    {
        
        $structureGraph = $this->_getExistingStructureGraph();

        $controllersDirectoryNode = $structureGraph->findNodeByContext('controllersDirectory');
        
        $controllerFileContext = ZendL_Tool_Project_Structure_Context_Registry::getInstance()->getContext('controllerFile');
        $newNode = new ZendL_Tool_Project_Structure_Node($controllerFileContext);
        $newNode->setBaseDirectory($controllersDirectoryNode->getContext()->getPath());
        $newNode->setControllerName($name);
        
        
        echo 'Creating new controller named \'' . $name . '\'' . PHP_EOL;
        $newNode->create();
        $controllersDirectoryNode->append($newNode);
        $this->_storeLoadedStructureGraph();
        
        if ($viewincluded) {
            $viewProvider = ZendL_Tool_Rpc_Provider_Registry::getInstance()->getProvider('View');
            $viewProvider->create($name, 'index');
        }
        
    }
    
}