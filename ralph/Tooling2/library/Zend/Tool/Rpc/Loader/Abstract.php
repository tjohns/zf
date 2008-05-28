<?php

abstract class Zend_Tool_Rpc_Loader_Abstract
{
    
    abstract protected function _getFiles();
    
    public function load()
    {
        $files = $this->_getFiles();
        $manifestRegistry = Zend_Tool_Rpc_Manifest_Registry::getInstance();
        $providerRegistry = Zend_Tool_Rpc_Provider_Registry::getInstance();
        
        $classesLoadedBefore = get_declared_classes();
        
        foreach ($files as $file) {
            Zend_Loader::loadFile($file);
        }
        
        $classesLoadedAfter = get_declared_classes();
        
        $loadedClasses = array_diff($classesLoadedAfter, $classesLoadedBefore);

        foreach ($loadedClasses as $loadedClass) {
            
            $reflectionClass = new ReflectionClass($loadedClass);
            
            if ($reflectionClass->implementsInterface('Zend_Tool_Rpc_Manifest_Interface') && !$reflectionClass->isAbstract())
            {
                $manifestRegistry->addManifest($reflectionClass->newInstance());
            }
            
            if ($reflectionClass->implementsInterface('Zend_Tool_Rpc_Provider_Interface') && !$reflectionClass->isAbstract())
            {
                $providerRegistry->addProvider($reflectionClass->newInstance());
            }
            
        }
        
    }
    
    
}