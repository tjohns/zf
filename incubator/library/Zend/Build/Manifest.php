<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Build
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/*
 * This is a singleton class which provides access to all the manifest files found on the classpath via
 * a constant-time-read data structure.
 */
class Zend_Build_Manifest
{
    const MANIFEST_FILE_PATTERN        = '/^[A-Z][0-9a-z].+-ZFManifest(....)$/';
    const ZEND_CONFIG_PACKAGE          = 'Zend_Config_';
    const CONSOLE_CONTEXT_CONFIG_NAME  = 'context';
    
    private static $_instance = null;
    private $_manifestArray = array(); 
    
    private function __construct()
    {
        // First get a list of all the manifest files on the include path
        $foundFiles = array();
        $includeDirs = explode(PATH_SEPARATOR, get_include_path());
        foreach($includeDirs as $dir)
        {
            $foundFiles = array_merge($foundFiles, $this->_recursiveSearch(self::MANIFEST_FILE_PATTERN, $dir));
        }
        
        // Now load all the manifest files
        foreach($foundFiles as $file)
        {
            $this->_loadManifestFile($file);
        }
    }
    
    private function __clone() {}   
    
    public function getInstance()
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new self;
        }
        
        return self::$_instance;
    }
    
    private function _recursiveSearch($pattern, $dir)
    {
        $contents = scandir($dir);
        $matches = array();
        foreach($contents as $content)
        {
            // Skip any directory that starts with a dot
            if(strpos($content, '.') === 0) {
                continue;
            }
            
            $fullPathContent = $dir . DIRECTORY_SEPARATOR . $content;
            if(is_dir($fullPathContent)) {
                $matches = array_merge($matches, $this->_recursiveSearch($pattern, $fullPathContent));
            } elseif(is_file($fullPathContent) && preg_match($pattern, $content)) {
                $matches[] = $fullPathContent;
            } else {
                // We'll skip links and any other file system objects
            }
        }
        return $matches;
    }
    
    private function _loadManifestFile($file)
    {
        // Figure out which config class to use and load it
        $extension = substr(strrchr($file, "."), 1);
        $configClass = self::ZEND_CONFIG_PACKAGE . ucfirst(strtolower($extension));
        try {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($configClass, explode(PATH_SEPARATOR, get_include_path()));
        }
        catch(Zend_Exception $e)
        {
            // Problem with loading the class
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception("Config class '$configClass' could not be found.");
        }
        
        $configs =  new $configClass($file);
        
        foreach($configs as $key => $value)
        {
            if($key != 'context') {
                continue;
            } else {
                $consoleContext = $value;
            }
            
            if(!isset($consoleContext->name))
            {
                // Problem with loading the class
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception("Console context in '$file' does not have required 'name' attribute.");
            }
            
            $name = $consoleContext->name;
            $alias = $consoleContext->alias;
            // Create separate sections so that different kinds of contexts can live in different namespaces
            $type = $consoleContext->type;
            
            // Create it under indexes for 'name' and 'shortname' after testing them
            if(isset($manifestArray[$type]) && isset($manifestArray[$type][$name])) {
                // Problem with loading the class
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception("Manifest already contains a context with name '$name' and type '$type'.");
            } else {
                $manifestArray[$type][$name] = $consoleContext;
            }
            
            if(isset($alias) && isset($manifestArray[$type]) && isset($manifestArray[$type][$alias])) {
                // Problem with loading the class
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception("Manifest already contains a console context with alias '$alias'");
            } else {
                $manifestArray[$type][$alias] = $consoleContext->toArray();
            }
        }
    }
    
    public function toConfig($allowModifications = true)
    {
        require_once('Zend/Config.php');
        return new Zend_Config(_manifestArray, $allowModifications);
    }
    
    public function toArray()
    {
        return array($this->_manifestArray);
    }
}