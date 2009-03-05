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
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract base class for bootstrap classes
 *
 * @uses       Zend_Application_Bootstrap_IBootstrap
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Application_Bootstrap_Base 
    implements Zend_Application_Bootstrap_IBootstrap, 
               Zend_Application_Bootstrap_IResourceBootstrap
{
    /**
     * @var Zend_Application|Zend_Application_Bootstrap_IBootstrap
     */
    protected $_application;

    /**
     * @var array Internal resource methods (resource/method pairs)
     */
    protected $_classResources = array();

    /**
     * @var string
     */
    protected $_environment;

    /**
     * @var array 
     */
    protected $_options = array();

    /**
     * @var Zend_Loader_PluginLoader_Interface
     */
    protected $_pluginLoader;

    /**
     * @var array Class-based resource plugins
     */
    protected $_pluginResources = array();

    /**
     * @var array Initializers that havebeen run
     */
    protected $_run = array();

    /**
     * Constructor
     *
     * Sets application object, initializes options, and prepares list of 
     * initializer methods.
     * 
     * @param  Zend_Application|Zend_Application_Bootstrap_IBootstrap $application
     * @return void
     * @throws Zend_Application_Bootstrap_Exception When invalid applicaiton is provided 
     */
    public function __construct($application)
    {
        if (($application instanceof Zend_Application) 
            || ($application instanceof Zend_Application_Bootstrap_IBootstrap)
        ) {
            $this->_application = $application;
        } else {
            throw new Zend_Application_Bootstrap_Exception('Invalid application provided to bootstrap constructor');
        }

        $options = $application->getOptions();
        $this->setOptions($options);

        foreach (get_class_methods($this) as $method) {
            if (5 < strlen($method) && '_init' === substr($method, 0, 5)) {
                $this->_classResources[strtolower(substr($method, 5))] = $method;
            }
        }
    }

    /**
     * Set class state
     * 
     * @param  array $options 
     * @return Zend_Application_Bootstrap_Base
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);

        if (array_key_exists('pluginPaths', $options)) {
            $pluginLoader = $this->getPluginLoader();
            
            foreach ($options['pluginPaths'] as $prefix => $path) {
                $pluginLoader->addPrefixPath($prefix, $path);
            }
            
            unset($options['pluginPaths']);
        }

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            
            if (in_array($method, $methods)) {
                $this->$method($value);
            } elseif ('Resources' == ucfirst($key)) {
                foreach ($value as $resource => $resourceOptions) {
                    $this->registerPluginResource($resource, $resourceOptions);
                }
            }
        }
        $this->_options = $this->_options + $options;
        return $this;
    }

    /**
     * Get current options from bootstrap
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Is an option present?
     * 
     * @param  string $key 
     * @return bool
     */
    public function hasOption($key)
    {
        return array_key_exists($key, $this->_options);
    }

    /**
     * Retrieve a single option
     * 
     * @param  string $key 
     * @return mixed
     */
    public function getOption($key)
    {
        if ($this->hasOption($key)) {
            return $this->_options[$key];
        }
        return null;
    }

    /**
     * Get class resources (as resource/method pairs)
     * 
     * @return array
     */
    public function getClassResources()
    {
        return $this->_classResources;
    }

    /**
     * Get class resource names
     * 
     * @return array
     */
    public function getClassResourceNames()
    {
        return array_keys($this->_classResources);
    }

    /**
     * Register a new resource plugin
     * 
     * @param  string|Zend_Application_Resource_IResource $resource
     * @param  mixed  $options
     * @return Zend_Application_Bootstrap_Base
     * @throws Zend_Application_Bootstrap_Exception When invalid resource is provided
     */
    public function registerPluginResource($resource, $options = null)
    {
        if ($resource instanceof Zend_Application_Resource_IResource) {
            $className  = get_class($resource);
            $pluginName = strtolower(substr(strrchr($resource, '_'), 1)); 
            $this->_pluginResources[$pluginName] = $resource;
            return $this;
        }

        if (!is_string($resource)) {
            throw new Zend_Application_Bootstrap_Exception('Invalid resource provided to ' . __METHOD__);
        }

        $resource = strtolower($resource);
        $this->_pluginResources[$resource] = $options;
        return $this;
    }

    /**
     * Unregister a resource from the bootstrap
     * 
     * @param  string|Zend_Application_Resource_IResource $resource 
     * @return Zend_Application_Bootstrap_Base
     * @throws Zend_Application_Bootstrap_Exception When unknown resource type is provided
     */
    public function unregisterPluginResource($resource)
    {
        if ($resource instanceof Zend_Application_Resource_IResource) {
            if ($index = array_search($resource, $this->_resources, true)) {
                unset($this->_pluginResources[$index]);
            }
            return $this;
        }

        if (!is_string($resource)) {
            throw new Zend_Application_Bootstrap_Exception('Unknown resource type provided to ' . __METHOD__);
        }

        $resource = strtolower($resource);
        if (array_key_exists($resource, $this->_resources)) {
            unset($this->_pluginResources[$resource]);
        }

        return $this;
    }

    /**
     * Is the requested plugin resource registered? 
     * 
     * @param  string $resource 
     * @return bool
     */
    public function hasPluginResource($resource)
    {
        $resource = strtolower($resource);
        return array_key_exists($resource, $this->_pluginResources);
    }
    
    /**
     * Get a registered plugin resource
     *
     * @param  string $resourceName
     * @return Zend_Application_Resource_IResource
     */
    public function getPluginResource($resource)
    {
        $resource = strtolower($resource);
        
        if (!array_key_exists($resource, $this->_pluginResources)) {
            return null;            
        }

        if (!$this->_pluginResources[$resource] instanceof Zend_Application_Resource_IResource) {
            $options   = $this->_pluginResources[$resource];
            $className = $this->getPluginLoader()->load($resource);
            $this->_pluginResources[$resource] = new $className($options);
        }

        $plugin = $this->_pluginResources[$resource];
        $plugin->setBootstrap($this);
        
        return $plugin;
    }

    /**
     * Retrieve all plugin resources
     * 
     * @return array
     */
    public function getPluginResources()
    {
        $resources = array();
        
        foreach (array_keys($this->_pluginResources) as $resource) {
            $resources[$resource] = $this->getPluginResource($resource);
        }
        
        return $resources;
    }

    /**
     * Retrieve plugin resource names
     * 
     * @return array
     */
    public function getPluginResourceNames()
    {
        return array_keys($this->_pluginResources);
    }

    /**
     * Set plugin loader for loading resources
     * 
     * @param  Zend_Loader_PluginLoader_Interface $loader 
     * @return Zend_Application_Bootstrap_Base
     */
    public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader)
    {
        $this->_pluginLoader = $loader;
        return $this;
    }
    
    /**
     * Get the plugin loader for resources
     *
     * @return Zend_Loader_PluginLoader_Interface
     */
    public function getPluginLoader()
    {
        if ($this->_pluginLoader === null) {
            $options = array(
                'Zend_Application_Resource' => 'Zend/Application/Resource'
            );

            $this->_pluginLoader = new Zend_Loader_PluginLoader($options);
        }

        return $this->_pluginLoader;
    }
    
    /**
     * Retrieve parent application instance
     * 
     * @return Zend_Application|Zend_Application_Bootstrap_IBootstrap
     */
    public function getApplication()
    {
        return $this->_application;
    }

    /**
     * Retrieve application environment
     * 
     * @return string
     */
    public function getEnvironment()
    {
        if (null === $this->_environment) {
            $this->_environment = $this->getApplication()->getEnvironment();
        }
        return $this->_environment;
    }

    /**
     * Bootstrap individual, all, or multiple resources
     * 
     * @param  null|string|array $resource
     * @return void
     * @throws Zend_Application_Bootstrap_Exception When invalid argument was passed 
     */
    public function bootstrap($resource = null)
    {
        if (null === $resource) {
            foreach ($this->getClassResourceNames() as $resource) {
                $this->_executeResource($resource);
            }
            
            foreach ($this->getPluginResourceNames() as $resource) {
                $this->_executeResource($resource);
            }
        } elseif (is_string($resource)) {
            $this->_executeResource($resource);
        } elseif (is_array($resource)) {
            foreach ($resource as $r) {
                $this->_executeResource($r);
            }
        } else {
            throw new Zend_Application_Bootstrap_Exception('Invalid argument passed to ' . __METHOD__);
        }
    }

    /**
     * Overloading: intercept calls to bootstrap<resourcename>() methods
     * 
     * @param  string $method 
     * @param  array  $args
     * @return void
     * @throws Zend_Application_Bootstrap_Exception On invalid method name 
     */
    public function __call($method, $args)
    {
        if (9 < strlen($method) && 'bootstrap' === substr($method, 0, 9)) {
            $resource = substr($method, 9);
            return $this->bootstrap($resource);
        }

        throw new Zend_Application_Bootstrap_Exception('Invalid method "' . $method . '"');
    }

    /**
     * Execute a resource
     *
     * Checks to see if the resource has already been run. If not, it searches 
     * first to see if a local method matches the resource, and executes that. 
     * If not, it checks to see if a plugin resource matches, and executes that 
     * if found.
     *
     * Finally, if not found, it throws an exception.
     * 
     * @param  string $resource 
     * @return void
     * @throws Zend_Application_Bootstrap_Exception When resource not found
     */
    protected function _executeResource($resource)
    {
        $resource = strtolower($resource);

        if (in_array($resource, $this->_run)) {
            return;
        }

        if (array_key_exists($resource, $this->_classResources)) {
            $method = $this->_classResources[$resource];
            $this->$method();
            $this->_markRun($resource);
            return;
        }

        if ($this->hasPluginResource($resource)) {
            $plugin = $this->getPluginResource($resource);
            $plugin->init();
            $this->_markRun($resource);
            return;
        }

        throw new Zend_Application_Bootstrap_Exception('Resource matching "' . $resource . '" not found');
    }

    /**
     * Mark a resource as having run
     * 
     * @param  string $resource 
     * @return void
     */
    protected function _markRun($resource)
    {
        if (!in_array($resource, $this->_run)) {
            $this->_run[] = $resource;
        }
    }
}
