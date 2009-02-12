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
     * Current environment
     * 
     * @var string
     */
    protected $_environment;

    /**
     * Resource autoloader
     *
     * @var Zend_Loader_Autoloader_Resource
     */
    protected $_resourceLoader = null;

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
     * Registered resources
     * 
     * @var array
     */
    protected $_resources = array();

    /**
     * Create a instance with options
     *
     * @param string $environment
     * @param mixed  $options
     */
    public function __construct($environment, $options = null)
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
            $method     = 'set' . ucfirst($key);
            
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
     * @return Zend_Application
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }
    
    /**
     * Register a new resource
     * 
     * @param  string $resource
     * @param  mixed  $options
     * @return Zend_Application
     */
    public function registerResource($resource, $options = null)
    {
        $resourceLoader = $this->getResourceLoader();
        $instance       = $resourceLoader->load($resource);
         
        $resourceName   = 
        
        $className  = $this->getPluginLoader()->load($plugin);
        $class      = new $className($options);
        $pluginName = strtolower(substr(strrchr($plugin, '_'), 1));
        
        $this->_resources[$pluginName] = $class;
        
        return $this;
    }
    
    /**
     * Get a registered resource
     *
     * @param  string $resourceName
     * @return Zend_Application_Bootstrap_Resource_Base
     */
    public function getResource($resourceName)
    {
        if (!isset($this->_resources[$resourceName])) {
            return null;            
        }

        return $this->_resource[$resourceName];
    }
    
    /**
     * Get the plugin loader for decorators
     *
     * @return Zend_Loader_PluginLoader
     */
    public function getResourceLoader()
    {
        if ($this->_resourceLoader === null) {
            $options = array('namespace' => 'Zend_Application_Bootstrap_Resource',
                             'basePath'  => 'Zend/Application/Bootstrap/Resource'));

            require_once 'Zend/Loader/Autoloader/Resource.php';
            $this->_resourceLoader = new Zend_Loader_Autoloader_Resource($options);
        }

        return $this->_resourceLoader;
    }
    
    /**
     * Init all resources
     *
     * @return Zend_Application
     */
    public function initAll()
    {
        foreach ($this->_resources as $resource) {
            $resource->init();
        }
        
        return $this;
    }
    
    /**
     * Method overloading for 'init' calls
     *
     * @param  string $name
     * @param  string $arguments
     * @throws Zend_Application_Exception When the called method is not known
     * @throws Zend_Application_Exception When resource is not registered
     * @return Zend_Application
     */
    public function __call($name, array $arguments)
    {
        if (strpos('init', $name) !== 0) {
            require_once 'Zend/Application/Exception.php';
            throw new Zend_Application_Exception(sprintf('Unknown method "%s" called', $name));
        }
        
        $resourceName = substr($name, 4);
        $resource     = $this->getResource($resourceName);
        
        if ($resource === null) {
            require_once 'Zend/Application/Exception.php';
            throw new Zend_Application_Exception(sprintf('Resource with name "%s" not registered', $resourceName));
        }
        
        $resource->init();

        return $this;
    }
}
