<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Project/Resource.php';
require_once 'Zend/Tool/Project/Context/Registry.php';
require_once 'Zend/Tool/Framework/Client/Registry.php';

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
        $profile = $this->_getExistingProfile();
        $viewScriptsDirectoryNode = $profile->findNodeByContext(array(
            'viewsDirectory', 'viewScriptsDirectory'
        ));

        $registry = Zend_Tool_Project_Context_Registry::getInstance();

        $newViewControllerScriptsDirectoryContext = $registry->getContext('ViewControllerScriptsDirectory');
        $newViewControllerScriptsDirectoryContext->setForControllerName($controllerName);

        $newViewScriptFileContext = $registry->getContext('ViewScriptFile');
        $newViewScriptFileContext->setScriptName($actionName);


        $newViewControllerScriptsDirectoryNode = new Zend_Tool_Project_Resource($newViewControllerScriptsDirectoryContext);
        $newViewScriptFileNode = new Zend_Tool_Project_Resource($newViewScriptFileContext);

        $newViewControllerScriptsDirectoryNode->append($newViewScriptFileNode);
        $newViewControllerScriptsDirectoryNode->recursivelySetBaseDirectory($viewScriptsDirectoryNode->getPath());
        $newViewControllerScriptsDirectoryNode->recursivelyCreate();

        $viewScriptsDirectoryNode->append($newViewControllerScriptsDirectoryNode);

        Zend_Tool_Framework_Client_Registry::getInstance()->response->appendContent(
            'Creating a view script.'
        );

        $this->_storeLoadedProfile();
    }
}