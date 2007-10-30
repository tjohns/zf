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
 * @package    Zend_Loader
 * @subpackage PluginLoader
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Loader_PluginLoader_Interface */
require_once 'Zend/Loader/PluginLoader/Interface.php';

/** Zend_Loader */
require_once 'Zend/Loader.php';

/**
 * Generic plugin class loader
 *
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage PluginLoader
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Loader_PluginLoader implements Zend_Loader_PluginLoader_Interface 
{
    /**
     * Static registry property
     *
     * @var array
     */
    static protected $_staticPrefixToPaths = array();
    
    /**
     * Instance registry property
     *
     * @var array
     */
    protected $_prefixToPaths = array();
    
    /**
     * Statically loaded helpers
     *
     * @var array
     */
    static protected $_staticLoadedHelpers = array();
    
    /**
     * Instance loaded helpers
     *
     * @var array
     */
    protected $_loadedHelpers = array();
    
    /**
     * Whether to use a statically named registry for loading helpers
     *
     * @var string|null
     */
    protected $_useStaticRegistry = null;
    
    /**
     * Constructor
     *
     * @param array $prefixToPaths
     * @param string $staticRegistryName OPTIONAL
     */
    public function __construct(Array $prefixToPaths = array(), $staticRegistryName = null)
    {
        
        if ($staticRegistryName != '' && is_string($staticRegistryName)) {
            $this->_useStaticRegistry = $staticRegistryName;
            self::$_staticPrefixToPaths[$staticRegistryName] = array();
            self::$_staticLoadedHelpers[$staticRegistryName] = array();
        }
        
        foreach ($prefixToPaths as $prefix => $path) {
            $this->addPrefixPath($prefix, $path);
        }

    }
    
    /**
     * Add prefixed paths to the registry of paths
     *
     * @param string $prefix
     * @param string $path
     * @return Zend_Loader_PluginLoader
     */
    public function addPrefixPath($prefix, $path)
    {
        if (!is_string($prefix) || !is_string($path)) {
            require_once 'Zend/Loader/PluginLoader/Exception.php';
            throw new Zend_Loader_PluginLoader_Exception('Zend_Loader_PluginLoader::addPrefixPath() method only takes strings for prefix and path.');
        }

        $prefix = rtrim($prefix, '_') . '_';
        $path   = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        if ($this->_useStaticRegistry) {
            self::$_staticPrefixToPaths[$this->_useStaticRegistry][$prefix][] = $path;
        } else {
            $this->_prefixToPaths[$prefix][] = $path;
        }
        return $this;
    }
    
    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * @param string $prefix
     * @param string $path OPTIONAL
     * @return Zend_Loader_PluginLoader
     */
    public function removePrefixPath($prefix, $path = null) 
    {
        if ($this->_useStaticRegistry) {
            $registry =& self::$_staticPrefixToPaths[$this->_useStaticRegistry];
        } else {
            $registry =& $this->_prefixToPaths;
        }
        
        if (!isset($registry[$prefix])) {
            require_once 'Zend/Loader/PluginLoader/Exception.php';
            throw new Zend_Loader_PluginLoader_Exception('Prefix ' . $prefix . ' was not found in the PluginLoader.');
        }
        
        if ($path != null) {
            $pos = array_search($path, $registry[$prefix]);
            if ($pos === null) {
                throw new Zend_Loader_PluginLoader_Exception('Prefix ' . $prefix . ' / Path ' . $path . ' was not found in the PluginLoader.');
            }
            unset($registry[$prefix][$pos]);
        } else {
            unset($registry[$prefix]);
        }
        
        return $this;        
    }
    
    /**
     * Whether or not a Helper by a specific name
     *
     * @param string $name
     * @return Zend_Loader_PluginLoader
     */
    public function isLoaded($name)
    {
        if ($this->_useStaticRegistry) {
            return array_key_exists($name, self::$_staticLoadedHelpers[$this->_useStaticRegistry]);
        } else {
            return array_key_exists($name, $this->_loadedHelpers);
        }
    }
    
    /**
     * Return full class name for a named helper
     *
     * @param string $name
     * @return string
     */
    public function getClassName($name)
    {
        // check to see if is loaded first?
        
        if ($this->_useStaticRegistry) {
            return self::$_staticLoadedHelpers[$this->_useStaticRegistry][$name];
        } else {
            return $this->_loadedHelpers[$name];
        }
    }
    
    /**
     * Load a helper via the name provided
     *
     * @param string $name
     * @return string
     */
    public function load($name)
    {
        if ($this->_useStaticRegistry) {
            $registry =& self::$_staticPrefixToPaths[$this->_useStaticRegistry];
        } else {
            $registry =& $this->_prefixToPaths;
        }

        if ($this->isLoaded($name)) {
            return $this->getClassName($name);
        }
        
        $found = false;
        
        foreach ($registry as $prefix => $paths) {
            foreach ($paths as $path) {
                
                $classFile = $name . '.php';
                $className = $prefix . $name;
                                
                if (class_exists($className)) {
                    $found = true;
                    break;
                }
                
                if (Zend_Loader::isReadable($path . $classFile)) {
                    Zend_Loader::loadFile($classFile, $path);
                    if (!class_exists($className)) {
                        throw new Zend_Loader_PluginLoader_Exception('File ' . $classFile . ' was loaded but class named ' . $className . ' was not found within it.');
                    }
                    $found = true;
                    break;
                }
            }
        }
        
        if ($found) {
            if ($this->_useStaticRegistry) {
                self::$_staticLoadedHelpers[$this->_useStaticRegistry][$name] = $className;
            } else {
                $this->_loadedHelpers[$name] = $className;
            }
            return $className;
        }

        require_once 'Zend/Loader/PluginLoader/Exception.php';
        throw new Zend_Loader_PluginLoader_Exception('Helper by ' . $name . ' was not found in the registry.');
    }
}
