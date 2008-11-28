<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';

class Zend_Controller_Tool_ActionProvider extends Zend_Tool_Project_Provider_Abstract 
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
    
    public function create($name, $controllername = 'index', $viewincluded = true)
    {
        $actionName = $name;
        
        $structureGraph = $this->_getExistingStructureGraph();

        $controllerFileNode = $structureGraph->findNodeByContext(array('controllerFile' => array('controllerName' => $controllername)));
        $controllerContext = $controllerFileNode->getContext();
        
        echo 'Adding action \'' . $actionName . '\' to controller \'' . $controllername . PHP_EOL;
        $controllerContext->addAction($actionName);
        
        if ($viewincluded) {
            $viewProvider = Zend_Tool_Framework_Provider_Registry::getInstance()->getProvider('View');
            $viewProvider->create($controllername, $actionName);
        }
        
    }
    
}