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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/*
 * This is a singleton class which provides access to all the manifest files found on the classpath via
 * a constant-time-read data structure.
 *
 * @category   Zend
 * @package    Zend_Build
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Manifest
{
    /**
     * @constant string
     */
    const MANIFEST_FILE_PATTERN        = '/^[A-Z][0-9a-z].+-ZFManifest(....)$/';

    /**
     * @constant string
     */
    const ZEND_CONFIG_PACKAGE          = 'Zend_Config_';

    /**
     * @constant string
     */
    const CONSOLE_CONTEXT_CONFIG_NAME  = 'context';

    /**
     * @var Object
     */
    private static $_instance = null;

    /**
     * Serves as a simple index in to the config array for this manifest instance.
     *
     * @var array
     */
    private $_configIndex = array();

    /**
     * Serves as a simple index in to the config array for this manifest instance.
     *
     * @var array
     */
    private $_configArray = array(); 

    /**
     * Constructor
     *
     * @return void
     */
    private function __construct() {}

    /**
     * __clone
     *
     * @return void
     */
    private function __clone() {}  

    /**
     * getInstance
     *
     * @return Zend_Build_Manifest
     */
    public function getInstance()
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * init
     *
     * @param  array $files
     * @return void
     */
    public function init(array $files = null)
    {
        // If files isn't specified, look for all valid ZFManifest files on the include path.
        if (!isset($files)) {
            $files = array();
            $includeDirs = explode(PATH_SEPARATOR, get_include_path());
            foreach ($includeDirs as $dir) {
                $files = array_merge($files, $this->_recursiveSearch(self::MANIFEST_FILE_PATTERN, $dir));
            }
        }

        // Now load all the manifest files
        foreach ($files as $file) {
            try {
                $this->_loadManifestFile($file);
            } catch(Zend_Build_Manifest_NameConflictException $e) {
                // Oftentimes include paths can overlap, so this should issue a warning and continue.
                continue;
            }
        }
    }

    /**
     * getContext
     *
     * @param  string $type
     * @param  string $name
     * @return mixed|null
     */
    public function getContext($type, $name)
    {
        if (array_key_exists($type, $this->_configIndex) && array_key_exists($name, $this->_configIndex[$type])) {
            return $this->_configIndex[$type][$name];
        } else {
            return null;
        }
    }

    /**
     * toConfig
     *
     * @param  boolean $allowModifications
     * @return Zend_Config
     */
    public function toConfig($allowModifications = true)
    {
        /**
         * @see Zend_Config
         */
        require_once('Zend/Config.php');
        return new Zend_Config($this->toArray(), $allowModifications);
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {   
        return $this->_configArray;
    }

    /**
     * _recursiveSearch
     *
     * @param  string $pattern
     * @param  string $dir
     * @return array
     */
    private function _recursiveSearch($pattern, $dir)
    {
        $contents = scandir($dir);
        $matches = array();
        foreach($contents as $content) {
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

    /**
     * _loadManifestFile
     *
     * @param  string $file
     * @throws Zend_Build_Exception
     * @throws Zend_Build_Manifest_NameConflictException
     * @return void
     */
    private function _loadManifestFile($file)
    {
        // Figure out which config class to use and load it
        $extension = substr(strrchr($file, "."), 1);

        if(strtolower($extension) != 'xml') {
            /**
             * @see Zend_Build_Exception
             */
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception("Currently XML is the only config format supported for manifest files." .
                                           "Extension '$extension' is invalid.");
        }

        $configClass = self::ZEND_CONFIG_PACKAGE . ucfirst(strtolower($extension));

        try {
            /**
             * @see Zend_Loader
             */
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($configClass, explode(PATH_SEPARATOR, get_include_path()));
        } catch(Zend_Exception $e) {
            // Problem with loading the config class
            /**
             * @see Zend_Build_Exception
             */
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception("Config class '$configClass' could not be found.");
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
                /**
                 * @see Zend_Build_Exception
                 */
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
                    /**
                     * @see Zend_Build_Manifest_NameConflictException
                     */
                    require_once 'Zend/Build/Manifest/NameConflictException.php';
                    throw new Zend_Build_Manifest_NameConflictException(
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
                    /**
                     * @see Zend_Build_Exception
                     */
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