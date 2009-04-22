<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';


class Zend_Tool_Project_Provider_View extends Zend_Tool_Project_Provider_Abstract
{
    
    public static function createResource(Zend_Tool_Project_Profile $profile, $controllerName, $actionName, $moduleName = null)
    {
        if (!is_string($controllerName)) {
            require_once 'Zend/Tool/Project/Provider/Exception.php';
            throw new Zend_Tool_Project_Provider_Exception('Zend_Tool_Project_Provider_View::createResource() expects \"controllerName\" is the name of a controller resource to create.');
        }
        
        if (!is_string($actionName)) {
            require_once 'Zend/Tool/Project/Provider/Exception.php';
            throw new Zend_Tool_Project_Provider_Exception('Zend_Tool_Project_Provider_View::createResource() expects \"actionName\" is the name of a controller resource to create.');
        }
        
        $profileSearchParams = array();
        
        if ($moduleName) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => $moduleName);
        }
        
        $profileSearchParams[] = 'viewsDirectory';
        $profileSearchParams[] = 'viewScriptsDirectory';

        if (($viewScriptsDirectory = $profile->search($profileSearchParams)) === false) {
            require_once 'Zend/Tool/Project/Provider/Exception.php';
            throw new Zend_Tool_Project_Provider_Exception('This project does not have a viewScriptsDirectory resource.');
        }
        
        $profileSearchParams['viewControllerScriptsDirectory'] = array('forControllerName' => $controllerName);
        
        // XXXXXXXXX below is failing b/c of above search params
        if (($viewControllerScriptsDirectory = $viewScriptsDirectory->search($profileSearchParams)) === false) {
            $viewControllerScriptsDirectory = $viewScriptsDirectory->createResource('viewControllerScriptsDirectory', array('forControllerName' => $controllerName));
        }
        
        $newViewScriptFile = $viewControllerScriptsDirectory->createResource('ViewScriptFile', array('forActionName' => $actionName));

        return $newViewScriptFile;
    }

    public function create($controllerName, $actionNameOrSimpleName)
    {
        
        if ($controllerName == '' || $actionName == '') {
            require_once 'Zend/Tool/Project/Provider/Exception.php';
            throw new Zend_Tool_Project_Provider_Exception('ControllerName and/or ActionName are empty.');
        }
        
        $profile = $this->_loadProfile();
        
        $view = self::createResource($profile, $controllerName, $actionName);
        
        if ($this->_registry->getRequest()->isPretend()) {
            $this->_registry->getResponse(
                'Would create a view script in location ' . $view->getContext()->getPath()
                );
        } else {
            $this->_registry->getResponse(
                'Creating a view script in location ' . $view->getContext()->getPath() 
                );
            $view->create();
            $this->_storeProfile();
        }
        
        /*
        $profile = $this->_getExistingProfile();
        $viewScriptsDirectoryNode = $profile->findNodeByContext(array(
            'viewsDirectory', 'viewScriptsDirectory'
        ));

        $registry = Zend_Tool_Project_Context_Repository::getInstance();

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

        Zend_Tool_Framework_Registry::getInstance()->response->appendContent(
            'Creating a view script.'
        );

        $this->_storeLoadedProfile();
        */
    }
}
