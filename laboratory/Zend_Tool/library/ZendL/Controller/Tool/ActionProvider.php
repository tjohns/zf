<?php

require_once 'ZendL/Tool/Project/Provider/Abstract.php';

class ZendL_Controller_Tool_ActionProvider extends ZendL_Tool_Project_Provider_Abstract 
{
    
    /*
    public function getContextClasses()
    {
        return array(
            'ZendL_Controller_Tool_ControllerFileContext', // Context: ControllerFile
            'ZendL_Controller_Tool_ControllersDirectoryContext' // Context: ControllersDirectory
            );
    }
    */
    
    public function create($name, $controllername = 'index', $viewincluded = true)
    {
        $actionName = $name;
        
        $structureGraph = $this->_getExistingStructureGraph();

        $controllerFileNode = $structureGraph->findNodeByContext(array('controllerFile' => array('controllerName' => $controllername)));
        $controllerContext = $controllerFileNode->getContext();
        
        echo 'Adding action \'' . $actionName . '\' to controller \'' . $controllername . PHP_EOL;
        $controllerContext->addAction($actionName);
        
        if ($viewincluded) {
            $viewProvider = ZendL_Tool_Rpc_Provider_Registry::getInstance()->getProvider('View');
            $viewProvider->create($controllername, $actionName);
        }
        
    }
    
}