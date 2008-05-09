<?php

class Zend_Tool_Provider_ZfProject_Controller extends Zend_Tool_Provider_ZfProject_ProviderAbstract 
{

    public function create($controllerName, $viewIncluded = true)
    {
        
        $projectProfile = $this->_getProjectProfile();
        
        $controllerDirectoryContext = $projectProfile->findContext('controllersDirectory');
        $controllerFile = $projectProfile->getContextByName('controllerFile');
        $controllerDirectoryContext->append($controllerFile);
        
        $controllerFile->setName($controllerName);
        $controllerFile->create();
        
        if ($viewIncluded) {
            $viewScriptsDirectory = $projectProfile->findContext('viewScriptsDirectory');
            $viewControllerScriptsDirectory = $projectProfile->getContextByName('viewControllerScriptsDirectory');
            $viewScriptsDirectory->append($viewControllerScriptsDirectory);
            $viewControllerScriptsDirectory->setName($controllerName);
            $viewControllerScriptsDirectory->create();
            
            
            $viewScriptFile = $projectProfile->getContextByName('viewScriptFile');
            $viewControllerScriptsDirectory->append($viewScriptFile);
            $viewScriptFile->create();
        }
        
        $projectProfileFile = $projectProfile->findContext('projectProfileFile');
        $projectProfileFile->refresh();
        
        
        $this->_response->setContent('Controller \'' . $controllerName . '\' has been created' . (($viewIncluded) ? ' with proper view scripts.' : '.'));
    }

    public function delete($controllerName, $viewIncluded = true)
    {
        $projectProfile = $this->_getProjectProfile();
        $controllerFile = $projectProfile->findContext(array('controllerFile' => array('name' => $controllerName)));
        $controllerFile->delete();
        
        if ($viewIncluded) {
            $viewScriptsDirectory = $projectProfile->findContext(array('viewControllerScriptsDirectory' => array('name' => $controllerName)));
            $viewScriptsDirectory->delete();
        }
        
        $projectProfileFile = $projectProfile->findContext('projectProfileFile');
        $projectProfileFile->refresh();
        
        $this->_response->setContent('Controller \'' . $controllerName . '\' has been deleted' . (($viewIncluded) ? ' with proper view scripts.' : '.'));
    }
    
}

