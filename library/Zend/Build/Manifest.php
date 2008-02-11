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
    protected $_manifestPattern        = '/^[A-Z][0-9a-z].+-ZFManifest(....)$/';
    protected $_configPrefix           = 'Zend_Config_';
    //const CONSOLE_CONTEXT_CONFIG_NAME  = 'context';

    
    private static $_instance = null;
    
    /**
     * var Serves as a simple index in to the config array for this manifest instance.
     */
    private $_configIndex = array();
    private $_configArray = array(); 


    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function resetInstance()
    {
    	self::$_instance = null;
    	return self::getInstance(); 
    }

    private function __construct()
    {}
    
    private function __clone()
    {}
    
    public function setManifestPattern($pattern)
    {
    	$this->_manifestPattern = $pattern;
    	return $this;
    }
    
    public function getManifestPattern()
    {
    	return $this->_manifestPattern;
    }
    
    public function scanIncludePath()
    {
    	$this->scanPath(explode(PATH_SEPARATOR, get_include_path()));
    }
    
    public function scanPath($paths)
    {
    	// this method will accept a single path
    	if (!is_array($paths)) {
            $paths = (array) $paths;
    	}
    	
    	$manifestFiles = array();
    	
    	// find all manifest files within the given paths
    	foreach ($paths as $path) {
    		$manifestFiles = array_merge($manifestFiles, $this->_recursiveSearch($path));
    	}

    	// load the manifest files
    	foreach ($manifestFiles as $manifestFile) {
    		$this->_loadManifestFile($manifestFile);
    	}
    	

    }


    public function getContext($type, $name)
    {
        if (array_key_exists($type, $this->_configIndex) && array_key_exists($name, $this->_configIndex[$type])) {
            return $this->_configIndex[$type][$name];
        } else {
            return null;
        }
    }
    
    public function toConfig($allowModifications = true)
    {
        require_once 'Zend/Config.php';
        return new Zend_Config($this->toArray(), $allowModifications);
    }
    
    public function toArray()
    {
        return $this->_configArray;
    }
    
    private function _recursiveSearch($directory)
    {
    	$manifests = array();
        
    	$dirIterator = new RecursiveDirectoryIterator($directory);
        
    	foreach (new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST) as $dirItem) {
    		if ($dirItem->isFile() && preg_match($this->_manifestPattern, $dirItem->getFilename())) {
                $manifests[] = $dirItem->getPathname();
    		}
    	}

    	return $manifests;
    }
    
    private function _loadManifestFile($file)
    {
        // Figure out which config class to use and load it
        $extension = substr(strrchr($file, "."), 1);

        if(strtolower($extension) != 'xml') {
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception("Currently XML is the only config format supported for manifest files." .
                                           "Extension '$extension' is invalid.");
        }
        
        $configClass = $this->_configPrefix . ucfirst(strtolower($extension));
        
        if (!class_exists($configClass, false)) {
	        try {
	            require_once 'Zend/Loader.php';
	            Zend_Loader::loadClass($configClass, explode(PATH_SEPARATOR, get_include_path()));
	        } catch(Zend_Exception $e) {
	            // Problem with loading the config class
	            require_once 'Zend/Build/Exception.php';
	            throw new Zend_Build_Exception("Config class '$configClass' could not be found.");
	        }
        }
        
        $configs =  new $configClass($file);
        
        foreach($configs as $key => $value) {
            if($key != 'context') {
                continue;
            } else {
                $consoleContext = $value;
            }
            
            // Check that 'name' is set first
            $name = $consoleContext->name;
            if(!isset($name)) {
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception("Console context in '$file' does not have required 'name' attribute.");
            }
            
            // Create separate sections in the index so that different kinds of contexts can live in different namespaces
            // and still be efficiently accessed.
            
            $type = $consoleContext->type;
            //Index it under key for 'name' after testing it to see if we've already added it
            if (isset($this->_configIndex[$type])) {
                if (isset($this->_configIndex[$type][$name])) {
                    // Problem with loading the class
                    require_once 'Zend/Build/Exception.php';
                    throw new Zend_Build_Exception(
                        "Manifest already contains a context with name '$name' and type '$type'."
                    );
                } else {
                    // Do nothing. Type already maps to an array in the index.
                }
            } else {
                // Need to create an array for type to map to.
                $this->_configIndex[$type] = array();
            }
            
            $this->_configArray[] = $consoleContext;
            $this->_configIndex[$type][$name] = $consoleContext;
            
            $alias = $consoleContext->alias;
            
            // Also index it under 'alias' for instant access
            if(isset($alias)) {
                if (isset($this->_configIndex[$type]) && isset($this->_configIndex[$type][$alias])) {
                    require_once 'Zend/Build/Exception.php';
                    throw new Zend_Build_Exception("Manifest already contains a console context with alias '$alias'");
                } else {
                    // Type must map to an array after handling the name above.
                    $this->_configIndex[$type][$alias] = $consoleContext;
                }
            }
        }
    }
}
