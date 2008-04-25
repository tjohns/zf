<?php

require_once 'Zend/Loader/PluginLoader.php';

class Zend_Tool_Manifest_Loader_PluginDirectory extends Zend_Tool_Manifest_Loader_Abstract
{

    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_pluginLoader = null;
    
    public function __construct(Array $prefixToPaths = array())
    {
        $this->_pluginLoader = new Zend_Loader_PluginLoader();
        
        if ($prefixToPaths) {
            $this->addPrefixToPaths($prefixToPaths);
        }

    }
    

    public function addPrefixToPaths($prefix, $path = null)
    {
        if (is_array($prefix)) {
            $prefixToPaths = $prefix;
        } else {
            $prefixToPaths = array($prefix => $path);
        }
        
        foreach ($prefixToPaths as $prefix => $path) {
            $this->_pluginLoader->addPrefixPath($prefix, $path);
        }
        
        return $this;
    }
    
    public function load()
    {
        $loadedClasses = $this->_pluginLoader->loadAll();
        
        foreach ($loadedClasses as $className) {
            $reflector = new ReflectionClass($className);
            if ($reflector->isInstantiable() && $reflector->isSubclassOf('Zend_Tool_Provider_Abstract')) {
                $this->addProvider($reflector->newInstance());
            }

            /*
            if (array_key_exists('Zend_Tool_Provider_Abstract', class_parents($className, false)) && ) {
                echo 'is a good one';
            }
            */
        }
        
    }
    
}