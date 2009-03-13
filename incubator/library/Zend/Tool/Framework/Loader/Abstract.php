<?php

require_once 'Zend/Tool/Framework/Registry.php';

abstract class Zend_Tool_Framework_Loader_Abstract
{
    
    abstract protected function _getFiles();
    
    public function load()
    {
        $files = $this->_getFiles();
        $clientRegistry = Zend_Tool_Framework_Registry::getInstance();
        $manifestRegistry = $clientRegistry->getManifestRepository();
        $providerRegistry = $clientRegistry->getProviderRepository();
        
        $classesLoadedBefore = get_declared_classes();
        
        $oldLevel = error_reporting(E_ALL | ~E_STRICT); // remove strict so that other packages wont throw warnings
        foreach ($files as $file) {
            // should we lint the files here? i think so
            include_once $file;
        }
        error_reporting($oldLevel); // restore old error level

        $classesLoadedAfter = get_declared_classes();
        
        $loadedClasses = array_diff($classesLoadedAfter, $classesLoadedBefore);

        foreach ($loadedClasses as $loadedClass) {
            
            $reflectionClass = new ReflectionClass($loadedClass);  
            if ($reflectionClass->implementsInterface('Zend_Tool_Framework_Manifest_Interface') && !$reflectionClass->isAbstract()) {
                $manifestRegistry->addManifest($reflectionClass->newInstance());
            }
            
            if ($reflectionClass->implementsInterface('Zend_Tool_Framework_Provider_Interface') && !$reflectionClass->isAbstract()) {
                $providerRegistry->addProvider($reflectionClass->newInstance());
            }
        }
    }
    
    
}