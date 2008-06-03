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

require_once 'Zend/Build/Manifest/Context.php';

/*
 * This is a singleton class which provides access to all the manifest files found on the classpath via
 * a constant-time-read data structure.
 */
class Zend_Build_Manifest
{
    protected static $_instance = null;
    protected $_manifestPattern        = '/^[A-Z][0-9a-z].+-ZFManifest(....)$/';
    protected $_configPrefix           = 'Zend_Config_';
    protected $_contexts = array();
    
    /**
     * var Serves as a simple index in to the config array for this manifest instance.
     */
    /*
    private $_configIndex = array();
    private $_configArray = array(); 
    */

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

    /**
     * getContextsByType
     *
     * @param string $type
     * @return array
     */
    public function getContextsByType($type)
    {
        return (isset($this->_contexts[$type])) ? $this->_contexts[$type] : array();
    }

    public function getContext($type, $name)
    {
        return (isset($this->_contexts[$type][$name])) ? $this->_contexts[$type][$name] : null; 
        
        /*
        if (array_key_exists($type, $this->_configIndex) && array_key_exists($name, $this->_configIndex[$type])) {
            return $this->_configIndex[$type][$name];
        } else {
            return null;
        }
        */
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
        
        $manifest = Zend_Build_Manifest_Context::fromXmlFile($file);
        
        foreach($manifest as $context) {
            if (!isset($this->_contextsByType[$context->getType()])) {
                $this->_contextsByType[$context->getType()] = array();
            }
            $this->_contexts[$context->getType()][$context->getName()] = $context;
            /* TODO - other indexes here? */
        }
        
    }

}
