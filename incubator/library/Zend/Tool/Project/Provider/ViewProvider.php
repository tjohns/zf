<?php

class Zend_Tool_Project_Provider_View extends Zend_Tool_Project_Provider_Abstract 
{
    
    /*
    public function getContextClasses()
    {
        return array(
            'Zend_View_Tool_ViewScriptFileContext',
            'Zend_View_Tool_ViewsDirectoryContext',
            'Zend_View_Tool_ViewScriptsDirectoryContext',
            'Zend_View_Tool_ViewControllerScriptsDirectoryContext',
            'Zend_View_Tool_ViewHelpersDirectoryContext',
            'Zend_View_Tool_ViewFiltersDirectoryContext'
            );
        
    }
    */
    
    public function create($controllerName, $actionName)
    {
        $structureGraph = $this->_getExistingStructureGraph();
        $viewScriptsDirectoryNode = $structureGraph->findNodeByContext(array('viewsDirectory', 'viewScriptsDirectory'));

        $newViewControllerScriptsDirectoryContext = Zend_Tool_Project_Structure_Context_Registry::getInstance()->getContext('ViewControllerScriptsDirectory');
        $newViewControllerScriptsDirectoryContext->setForControllerName($controllerName);
        
        $newViewScriptFileContext = Zend_Tool_Project_Structure_Context_Registry::getInstance()->getContext('ViewScriptFile');
        $newViewScriptFileContext->setScriptName($actionName);        
        
        
        $newViewControllerScriptsDirectoryNode = new Zend_Tool_Project_Structure_Node($newViewControllerScriptsDirectoryContext);
        $newViewScriptFileNode = new Zend_Tool_Project_Structure_Node($newViewScriptFileContext);
        
        $newViewControllerScriptsDirectoryNode->append($newViewScriptFileNode);
        $newViewControllerScriptsDirectoryNode->recursivelySetBaseDirectory($viewScriptsDirectoryNode->getPath());
        $newViewControllerScriptsDirectoryNode->recursivelyCreate();
        
        $viewScriptsDirectoryNode->append($newViewControllerScriptsDirectoryNode);
        
        echo 'Creating a view script.' . PHP_EOL;
        
        $this->_storeLoadedStructureGraph();
    }
}
