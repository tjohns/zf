<?php

class ZendL_View_Tool_ViewProvider extends ZendL_Tool_Project_Provider_Abstract 
{
    
    public function getContextClasses()
    {
        return array(
            'ZendL_View_Tool_ViewScriptFileContext',
            'ZendL_View_Tool_ViewsDirectoryContext',
            'ZendL_View_Tool_ViewScriptsDirectoryContext',
            'ZendL_View_Tool_ViewControllerScriptsDirectoryContext',
            'ZendL_View_Tool_ViewHelpersDirectoryContext',
            'ZendL_View_Tool_ViewFiltersDirectoryContext'
            );
        
    }
    
    public function create($controllerName, $actionName)
    {
        $structureGraph = $this->_getExistingStructureGraph();
        $viewScriptsDirectoryNode = $structureGraph->findNodeByContext(array('viewsDirectory', 'viewScriptsDirectory'));

        $newViewControllerScriptsDirectoryContext = ZendL_Tool_Project_Structure_Context_Registry::getInstance()->getContext('ViewControllerScriptsDirectory');
        $newViewControllerScriptsDirectoryContext->setForControllerName($controllerName);
        
        $newViewScriptFileContext = ZendL_Tool_Project_Structure_Context_Registry::getInstance()->getContext('ViewScriptFile');
        $newViewScriptFileContext->setScriptName($actionName);        
        
        
        $newViewControllerScriptsDirectoryNode = new ZendL_Tool_Project_Structure_Node($newViewControllerScriptsDirectoryContext);
        $newViewScriptFileNode = new ZendL_Tool_Project_Structure_Node($newViewScriptFileContext);
        
        $newViewControllerScriptsDirectoryNode->append($newViewScriptFileNode);
        $newViewControllerScriptsDirectoryNode->recursivelySetBaseDirectory($viewScriptsDirectoryNode->getPath());
        $newViewControllerScriptsDirectoryNode->recursivelyCreate();
        
        $viewScriptsDirectoryNode->append($newViewControllerScriptsDirectoryNode);
        
        echo 'Creating a view script.' . PHP_EOL;
        
        $this->_storeLoadedStructureGraph();
    }
}
