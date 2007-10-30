<?php

interface Zend_Loader_PluginLoader_Interface
{
    
    /**
     * Add prefixed paths to the registry of paths
     *
     * @param string $prefix
     * @param string $path
     * @return Zend_Loader_PluginLoader
     */
    public function addPrefixPath($prefix, $path);
    
    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * @param string $prefix
     * @param string $path OPTIONAL
     * @return Zend_Loader_PluginLoader
     */
    public function removePrefixPath($prefix, $path = null);
    
    /**
     * Whether or not a Helper by a specific name
     *
     * @param string $name
     * @return Zend_Loader_PluginLoader
     */
    public function isLoaded($name);

    /**
     * Return full class name for a named helper
     *
     * @param string $name
     * @return string
     */
    public function getClassName($name);
    
    /**
     * Load a helper via the name provided
     *
     * @param string $name
     * @return string
     */
    public function load($name);
        
}
