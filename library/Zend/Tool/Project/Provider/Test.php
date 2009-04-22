<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Project/Provider/Exception.php';

class Zend_Tool_Project_Provider_Test extends Zend_Tool_Project_Provider_Abstract
{
    
    protected $_specialties = array('Application', 'Library');
    
    public static function isTestingEnabled(Zend_Tool_Project_Profile $profile)
    {
        $profileSearchParams = array('testsDirectory');
        $testsDirectory = $profile->search($profileSearchParams);
        
        return $testsDirectory->isEnabled();
    }
    
    public static function createApplicationResource(Zend_Tool_Project_Profile $profile, $controllerName, $actionName, $moduleName = null)
    {
        if (!is_string($controllerName)) {
            throw new Zend_Tool_Project_Provider_Exception('Zend_Tool_Project_Provider_View::createApplicationResource() expects \"controllerName\" is the name of a controller resource to create.');
        }
        
        if (!is_string($actionName)) {
            throw new Zend_Tool_Project_Provider_Exception('Zend_Tool_Project_Provider_View::createApplicationResource() expects \"actionName\" is the name of a controller resource to create.');
        }
        
        $testsDirectoryResource = $profile->search('testsDirectory');
        
        if (($testAppDirectoryResource = $testsDirectoryResource->search('testApplicationDirectory')) === false) {
            $testAppDirectoryResource = $testsDirectoryResource->createResource('testApplicationDirectory');
        }
        
        if ($moduleName) {
            //@todo $moduleName
            $moduleName = '';
        }
        
        if (($testAppControllerDirectoryResource = $testAppDirectoryResource->search('testApplicationControllerDirectory')) === false) {
            $testAppControllerDirectoryResource = $testAppDirectoryResource->createResource('testApplicationControllerDirectory');
        }
        
        $testAppControllerFileResource = $testAppControllerDirectoryResource->createResource('testApplicationControllerFile', array('forControllerName' => $controllerName));
        
        return $testAppControllerFileResource;
    }

    public static function createLibraryResource(Zend_Tool_Project_Profile $profile, $libraryClassName)
    {
        $testLibraryDirectoryResource = $profile->search(array('TestsDirectory', 'TestLibraryDirectory'));
        
        
        $fsParts = explode('_', $libraryClassName);
        
        $currentDirectoryResource = $testLibraryDirectoryResource;
        
        while ($nameOrNamespacePart = array_shift($fsParts)) {

            if (count($fsParts) > 0) {
                
                if (($libraryDirectoryResource = $currentDirectoryResource->search(array('TestLibraryNamespaceDirectory' => array('namespaceName' => $nameOrNamespacePart)))) === false) {
                    $currentDirectoryResource = $currentDirectoryResource->createResource('TestLibraryNamespaceDirectory', array('namespaceName' => $nameOrNamespacePart));
                } else {
                    $currentDirectoryResource = $libraryDirectoryResource;
                }

                
            } else {
                
                if (($libraryFileResource = $currentDirectoryResource->search(array('TestLibraryFile' => array('forClassName' => $libraryClassName)))) === false) {
                    $libraryFileResource = $currentDirectoryResource->createResource('TestLibraryFile', array('forClassName' => $libraryClassName));
                }
                
            }
            
        }
        
        return $libraryFileResource;
    }
    
    public function enable()
    {
        
    }
    
    public function disable()
    {
        
    }
    
    public function create($libraryClassName)
    {
        $profile = $this->_loadProfile();
        
        if (!self::isTestingEnabled($profile)) {
            $this->_registry->getResponse()->appendContent('Testing is not enabled for this project.');
        }
        
        $testLibraryResource = self::createLibraryResource($profile, $libraryClassName);
        
        $response = $this->_registry->getResponse();
        
        if ($this->_registry->getRequest()->isPretend()) {
            $response->appendContent('Would create a library stub in location ' . $testLibraryResource->getContext()->getPath());
        } else {
            $response->appendContent('Creating a library stub in location ' . $testLibraryResource->getContext()->getPath());
            $testLibraryResource->create();
            $this->_storeProfile();
        }
        
    }
    
}
