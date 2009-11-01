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
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Db_Table_Plugin_Interface
 */
require_once 'Zend/Db/Table/Plugin/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Table_Plugin_Broker
{

    const ALL           = 'global';
    const PREPEND       = 'prepend';
    const APPEND        = 'append';
    const BEFORE_PLUGIN = 'before';
    const AFTER_PLUGIN  = 'after';


    /**
     * Array of instance of objects implementing Zend_Db_Table_Plugin_Interface
     *
     * @var array
     */
    static protected $_stack = array();


    /**
     * Array of plugin classnames
     *
     * @var array
     */
    static protected $_plugins = array();


    /**
     * Array of plugin prefixs
     *
     * @var array
     */
    static protected $_prefix = array('');


    /**
     * Array of plugin paths
     *
     * @var array
     */
    static protected $_path = array();


    /**
     * Array of table classnames and applicable plugins
     *
     * @var array
     */
    static protected $_table = array(self::ALL => array());


    /**
     * Plugin loaders
     * @var array
     */
    static protected $_loader;


    /**
     * Register a plugin.
     *
     * @param  mixed  $plugin      An instance, string name or array parameters of a plugin
     * @param  mixed  $className   String or Array of table classnames to apply plugin
     * @param  string $position    Where to insert the new plugin in execution order
     * @param  mixed  $positionKey Used to identify existing plugins by classname OPTIONAL
     * @throws Zend_Db_Table_Plugin_Exception
     */
    static public function registerPlugin($plugin, $className = self::ALL, $position = self::APPEND,
                                          $positionKey = null)
    {
        if (self::hasPlugin($plugin)) {
            require_once 'Zend/Db/Table/Plugin/Exception.php';
            throw new Zend_Db_Table_Plugin_Exception('Plugin "' . get_class($plugin) . '" already registered.');
        }

        if (!($plugin instanceof Zend_Db_Table_Plugin_Interface)) {
            $plugin = self::getPluginInstance($plugin);
        }

        switch ($position) {
            case (self::BEFORE_PLUGIN):
            case (self::AFTER_PLUGIN):
                if (!self::hasPlugin($positionKey)) {
                    require_once 'Zend/Db/Table/Plugin/Exception.php';
                    throw new Zend_Db_Table_Plugin_Exception('Plugin "' . $position . '" has not been registered.');
                }
                $index = array_search(self::getPlugin($positionKey), self::$_stack, true);
                if ($position == self::AFTER_PLUGIN) {
                    $index++;
                }
                array_splice(self::$_stack, $index, 0, $plugin);
                break;

            case (self::APPEND):
                array_push(self::$_stack, $plugin);
                break;

            case (self::PREPEND):
                array_unshift(self::$_stack, $plugin);
                break;

            default:
                require_once 'Zend/Db/Table/Plugin/Exception.php';
                throw new Zend_Db_Table_Plugin_Exception('Position value "' . $position . '" is invalid.');
                break;
        }
        
        foreach ((array) $className as $class) {
            self::$_table[$class] = $plugin;
        }
    }

    /**
     * Unregister a plugin.
     *
     * @param string|Zend_Db_Table_Plugin_Interface $plugin Plugin object or class name
     * @throws Zend_Db_Table_Plugin_Exception
     */
    static public function unregisterPlugin($plugin)
    {
        if (!self::hasPlugin($plugin)) {
            require_once 'Zend/Db/Table/Plugin/Exception.php';
            throw new Zend_Db_Table_Plugin_Exception('Plugin has not been registered.');
        }
        
        if ($plugin instanceof Zend_Db_Table_Plugin_Interface) {
            $plugin = get_class($plugin);
        }
        
        unset(self::$_plugins[$plugin]);
        
        foreach (self::$_stack as $key => $instance) {
            if (get_class($instance) === $plugin) {
                unset(self::$_stack[$key]);
            }
        }
    }

    /**
     * Is a plugin of a particular class registered?
     *
     * @param string|Zend_Db_Table_Plugin_Interface $plugin Plugin object or class name
     * @return bool
     */
    static public function hasPlugin($className)
    {
        if ($className instanceof Zend_Db_Table_Plugin_Interface) {
            $className = get_class($className);
        }

        foreach (self::$_prefix as $prefix) {
            if (array_key_exists($prefix . $className, self::$_plugins)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Notify plugins of an event
     * 
     * @param  Zend_Loader_PluginLoader_Interface $loader 
     */
    static public function notify($className = null, $suffix = null, Array &$args = array())
    {
        $plugins = Zend_Db_Table_Plugin_Broker::getPlugins($className);

        if (!$plugins) {
            return false;
        }

        $method     = array_shift($args);
        $ret        = count($plugins);
        $stack      = false;

        foreach ($plugins as $plugin) {
            $class = get_class($plugin);
            if (!method_exists($plugin, $method . $suffix)) {
                require_once 'Zend/Db/Table/Row/Exception.php';
                throw new Zend_Db_Table_Row_Exception("Cannot notify non-existing event '{$method}' in plugin '{$class}'");
            }
            $result = call_user_func_array(array($plugin, $method . $suffix), $args);
            if ($result === false) {
                $ret = false;
                break;
            }
        }

        return $ret;
    }
    
    /**
     * Set plugin loader
     * 
     * @param  Zend_Loader_PluginLoader_Interface $loader 
     */
    static public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader)
    {
        self::$_loader = $loader;
    }

    /**
     * Retrieve the plugin loader
     *
     * @return Zend_Loader_PluginLoader
     */
    static public function getPluginLoader()
    {
        if (self::$_loader === null) {
            self::$_loader = new Zend_Loader_PluginLoader();
        }
        
        return self::$_loader;
    }

    /**
     * Add prefix path for plugin loader
     *
     * @param  string $prefix
     * @param  string $path
     */
    static public function addPrefixPath($prefix, $path)
    {
        self::getPluginLoader()->addPrefixPath($prefix, $path);
    }

    /**
     * Add many prefix paths at once
     * 
     * @param  array $spec 
     */
    public function addPrefixPaths(array $spec)
    {
        if (isset($spec['prefix']) && isset($spec['path'])) {
            return self::addPrefixPath($spec['prefix'], $spec['path']);
        } 
        foreach ($spec as $paths) {
            if (isset($paths['prefix']) && isset($paths['path'])) {
                self::addPrefixPath($paths['prefix'], $paths['path']);
            }
        }
    }

    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string|array $class Class name or array of class names of plugin(s) desired
     * @return false|array         Returns false if none found or an array of plugins if multiple 
     *                             plugins of same class found
     * @throws Zend_Db_Table_Plugin_Exception
     */
    static public function getPlugin($class)
    {
        $found = array();
        $class = (array) $class;

        foreach (self::$_stack as $plugin) {
            if (in_array(get_class($plugin), $class)) {
                $found[] = $plugin;
            }
        }

        switch (count($found)) {
            case 0:
                require_once 'Zend/Db/Table/Plugin/Exception.php';
                throw new Zend_Db_Table_Plugin_Exception('Plugin has not been registered.');

            default:
                return $found;
        }
    }

    /**
     * Retrieve all plugins
     *
     * @return array
     */
    static public function getPlugins()
    {
        return self::$_stack;
    }

    /**
     * Retrieve an instanceof a plugin
     *
     * @param  string|array $plugin Class name or array of class name and constructor options
     * @return Zend_Db_Table_Plugin_Interface
     * @throws Zend_Db_Table_Plugin_Exception
     */
    static public function getPluginInstance($plugin)
    {
        $args = array();

        if (is_array($plugin)) {
            $args = $plugin;
            $plugin = array_shift($args);
        }
        
        $className = self::getPluginLoader()->load(ucfirst($plugin));
        $class = new ReflectionClass($className);

        if (!$class->implementsInterface('Zend_Db_Table_Plugin_Interface')) {
            require_once 'Zend/Db/Table/Plugin/Exception.php';
            throw new Zend_Db_Table_Plugin_Exception('Plugin must implement interface "Zend_Db_Table_Plugin_Interface".');
        }

        if ($class->hasMethod('__construct')) {
            $object = $class->newInstanceArgs($args);
        } else {
            $object = $class->newInstance();
        }

        return $object;
    }

    /**
     * Retrieve all plugins for a table class
     *
     * @param  string|Zend_Db_Table_Abstract $tableClass Class name or instance of target table
     * @return array
     */
    static public function getTablePlugins($tableClass)
    {
        if ($tableClass instanceof Zend_Db_Table_Abstract) {
            $tableClass = get_class($tableClass);
        }
        
        if (isset(self::$_table[$tableClass])) {
            $plugins = array_merge(self::$_table[self::ALL], self::$_table[$tableClass]);
        } else {
            $plugins = self::$_table[self::ALL];
        }

        return self::getPlugin($plugins);
    }
}
