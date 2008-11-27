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
 * @package    Zend_Application
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Application
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application
{
    /**
     * Plugin loader for decorators
     *
     * @var Zend_Loader_PluginLoader
     */
    protected $_pluginLoader = null;

    /**
     * Option keys to skip when calling setOptions()
     *
     * @var array
     */
    protected $_skipOptions = array(
        'options',
        'config',
    );
    
    /**
     * Registered plugins
     * 
     * @var array
     */
    protected $_plugins = array();

    /**
     * Create a instance with options
     *
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } else if ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set options from array
     *
     * @param  array $options Configuration for Zend_Application
     * @return Zend_Application
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method) && !in_array(strtolower($key), $this->_skipOptions)) {
                $this->$method($value);
            } else if (($plugin = $this->getPlugin($key)) !== null) {
                $plugin->setOptions($value);
            } else {
                $this->registerPlugin($key, $value);
            } 
        }

        return $this;
    }

    /**
     * Set options from config object
     *
     * @param  Zend_Config $config Configuration for Zend_Application
     * @return Zend_TagCloud
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }
    
    /**
     * Register a new plugin
     * 
     * @param  string $plugin
     * @param  mixed  $options
     * @return Zend_Application
     */
    public function registerPlugin($plugin, $options = null)
    {
        $pluginLoader = $this->getPluginLoader();
        
        $className  = $this->getPluginLoader()->load($plugin);
        $class      = new $className($options);
        $pluginName = substr(strrchr($plugin, '_'), 1);
        
        
        $this->_plugins[$pluginName] = $class;
        
        return $this;
    }
    
    /**
     * Get a registered plugin
     *
     * @param  string $pluginName
     * @return Zend_Application_Plugin
     */
    public function getPlugin($pluginName)
    {
        if (!isset($this->_plugins[$pluginName])) {
            return null;            
        }

        return $this->_plugins[$pluginName];
    }
    
    /**
     * Get the plugin loader for decorators
     *
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader()
    {
        if ($this->_pluginLoader === null) {
            $prefix     = 'Zend_Application_Plugin_';
            $pathPrefix = 'Zend/Application/Plugin/';

            require_once 'Zend/Loader/PluginLoader.php';
            $this->_pluginLoader = new Zend_Loader_PluginLoader(array($prefix => $pathPrefix));
        }

        return $this->_pluginLoader;
    }
    
    /**
     * Init all plugins
     *
     * @return Zend_Application
     */
    public function initAll()
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->init();
        }
        
        return $this;
    }
    
    /**
     * Method overloading for 'init' calls
     *
     * @param  string $name
     * @param  string $arguments
     * @throws Zend_Application_Exception When the called method is not known
     * @throws Zend_Application_Exception When plugin is not registered
     * @return Zend_Application
     */
    public function __call($name, array $arguments)
    {
        if (strpos('init', $name) !== 0) {
            require_once 'Zend/Application/Exception.php';
            throw new Zend_Application_Exception(sprintf('Unknown method "%s" called', $name));
        }
        
        $pluginName = substr($name, 4);
        $plugin     = $this->getPlugin($pluginName);
        
        if ($plugin === null) {
            require_once 'Zend/Application/Exception.php';
            throw new Zend_Application_Exception(sprintf('Plugin with name "%s" not registered', $pluginName));
        }
        
        $plugin->init();

        return $this;
    }
}
