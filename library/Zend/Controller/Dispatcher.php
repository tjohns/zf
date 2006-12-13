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
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 

/** Zend */
require_once 'Zend.php';

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Dispatcher implements Zend_Controller_Dispatcher_Interface
{
    /**
     * Current dispatchable directory
     * @var string
     */
    protected $_curDirectory;

    /**
     * Default action name; defaults to 'index'
     * @var string 
     */
    protected $_defaultAction = 'index';

    /**
     * Default controller name; defaults to 'index'
     * @var string 
     */
    protected $_defaultController = 'index';

    /**
     * Directories where Zend_Controller_Action files are stored.
     * @var array
     */
    protected $_directories = array();

    /**
     * Array of invocation parameters to use when instantiating action 
     * controllers
     * @var array 
     */
    protected $_invokeParams = array();

    /**
     * Response object to pass to action controllers, if any
     * @var Zend_Controller_Response_Abstract|null 
     */
    protected $_response = null;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }

    /**
     * Formats a string into a controller name.  This is used to take a raw
     * controller name, such as one that would be packaged inside a Zend_Controller_Dispatcher_Token
     * object, and reformat it to a proper class name that a class extending
     * Zend_Controller_Action would use.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatControllerName($unformatted)
    {
        return ucfirst($this->_formatName($unformatted)) . 'Controller';
    }


    /**
     * Formats a string into an action name.  This is used to take a raw
     * action name, such as one that would be packaged inside a Zend_Controller_Dispatcher_Token
     * object, and reformat into a proper method name that would be found
     * inside a class extending Zend_Controller_Action.
     *
     * @todo Should action method names allow underscores?
     * @param string $unformatted
     * @return string
     */
    public function formatActionName($unformatted)
    {
        $formatted = $this->_formatName($unformatted, false);
        return strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . 'Action';
    }


    /**
     * Formats a string from a URI into a PHP-friendly name.  Replaces words
     * separated by "-", "_", or "." with camelCaps and removes any characters
     * that are not alphanumeric.
     *
     * If $preserveUnderscores is true, underscores are retained, and the word 
     * following Title-cased.
     *
     * @param string $unformatted
     * @param boolean $preserveUnderscores Defaults to true
     * @return string
     */
    protected function _formatName($unformatted, $preserveUnderscores = true)
    {
        if ($preserveUnderscores) {
            $unformatted = str_replace(array('-', '.'), ' ', strtolower($unformatted));
        } else {
            $unformatted = str_replace(array('-', '.', '_'), ' ', strtolower($unformatted));
        }
        $unformatted = preg_replace('/[^a-z0-9_ ]/', '', $unformatted);
        $unformatted = str_replace(' ', '', ucwords($unformatted));

        $unformatted = str_replace('_', ' ', $unformatted);
        return str_replace(' ', '_', ucwords($unformatted));
    }

    /**
     * Add a single path to the controller directory stack
     * 
     * @param string $path 
     * @return Zend_Controller_Dispatcher
     */
    public function addControllerDirectory($path, $module = null)
    {
        if (!is_string($path) || !is_dir($path) || !is_readable($path)) {
            throw Zend::exception('Zend_Controller_Dispatcher_Exception', "Directory \"$path\" not found or not readable");
        }

        if (null === $module) {
            $this->_directories[] = rtrim($path, '\//');
        } else {
            $this->_directories[(string) $module] = rtrim($path, '\//');
        }

        return $this;
    }

    /**
     * Sets the directory(ies) where the Zend_Controller_Action class files are stored.
     *
     * @param string|array $path
     * @return Zend_Controller_Dispatcher
     */
    public function setControllerDirectory($path)
    {
        $dirs = (array) $path;
        foreach ($dirs as $key => $dir) {
            if (!is_dir($dir) or !is_readable($dir)) {
                throw Zend::exception('Zend_Controller_Dispatcher_Exception', "Directory \"$dir\" not found or not readable");
            }
            $dirs[$key] = rtrim($dir, '/\\');
        }

        $this->_directories = $dirs;
        return $this;
    }

    /**
     * Return the currently set directory for Zend_Controller_Action class 
     * lookup
     * 
     * @return string
     */
    public function getControllerDirectory()
    {
        return $this->_directories;
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be dispatched to a controller.
     * This only verifies that the Zend_Controller_Action can be dispatched and does not
     * guarantee that the action will be accepted by the Zend_Controller_Action.
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     * @throws Zend_Controller_Dispatcher_Exception
     */
    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        $dispatchable = $this->_getController($request);

        return is_string($dispatchable);
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     * 
     * @param string $name
     * @param mixed $value 
     * @return Zend_Controller_Dispatcher
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     * 
     * @param array $params 
     * @return Zend_Controller_Dispatcher
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     * 
     * @param string $name 
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_invokeParams;
    }

    /**
     * Clear the controller parameter stack
     * 
     * By default, clears all parameters. If a parameter name is given, clears 
     * only that parameter; if an array of parameter names is provided, clears 
     * each.
     * 
     * @param null|string|array single key or array of keys for params to clear
     * @return Zend_Controller_Dispatcher
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->_invokeParams = array();
        } elseif (is_string($name) && isset($this->_invokeParams[$name])) {
            unset($this->_invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->_invokeParams[$key])) {
                    unset($this->_invokeParams[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Set response object to pass to action controllers
     * 
     * @param Zend_Controller_Response_Abstract|null $response 
     * @return Zend_Controller_Dispatcher
     */
    public function setResponse(Zend_Controller_Response_Abstract $response = null)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Return the registered response object
     * 
     * @return Zend_Controller_Response_Abstract|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set the default controller (minus any formatting)
     * 
     * @param string $controller 
     * @return Zend_Controller_Dispatcher
     */
    public function setDefaultController($controller)
    {
        $this->_defaultController = (string) $controller;
    }

    /**
     * Retrive the default controller name (minus formatting)
     * 
     * @return string
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }

    /**
     * Set the default action (minus any formatting)
     * 
     * @param string $action 
     * @return Zend_Controller_Dispatcher
     */
    public function setDefaultAction($action)
    {
        $this->_defaultAction = (string) $action;
    }

    /**
     * Retrive the default action name (minus formatting)
     * 
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * Dispatch to a controller/action
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return boolean
     */
    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);

        /**
         * Get controller directories
         */
        $directories  = $this->getControllerDirectory();

        /**
         * Get controller class
         */
        $className = $this->_getController($request, $directories);

        /**
         * If no class name returned, report exceptional behaviour
         */
        if (!$className) {
            throw Zend::exception('Zend_Controller_Dispatcher_Exception', '"' . $request->getControllerName() . '" controller does not exist');
        }

        /**
         * Load the controller class file
         *
         * Attempts to load the controller class file from {@link getDispatchDirectory()}, 
         * using the module prefix if a module was requested.
         */
        $moduleClass = $this->_getModuleClass($request, $className);
        if ($className != $moduleClass) {
            $classLoaded = $this->loadClass($moduleClass, $this->getDispatchDirectory());
            if (!$classLoaded) {
                Zend::loadClass($className, $this->getDispatchDirectory());
            } else {
                $className = $classLoaded;
            }
        } else {
            Zend::loadClass($className, $this->getDispatchDirectory());
        }

        /**
         * Instantiate controller with request, response, and invocation 
         * arguments; throw exception if it's not an action controller
         */
        $controller = new $className($request, $this->getResponse(), $this->getParams());
        if (!$controller instanceof Zend_Controller_Action) {
            throw Zend::exception('Zend_Controller_Dispatcher_Exception', "Controller '$className' is not an instance of Zend_Controller_Action");
        }

        /**
         * Retrieve the action name
         */
        $action = $this->_getAction($request);

        /**
         * If method does not exist, default to __call()
         */
        $doCall = !method_exists($controller, $action);

        /**
         * Dispatch the method call
         */
        $request->setDispatched(true);
        $controller->preDispatch();
        if ($request->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            if ($doCall) {
                $controller->__call($action, array());
            } else {
                $controller->$action();
            }
            $controller->postDispatch();
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
    }

    /**
     * Get controller name
     *
     * Try request first; if not found, try pulling from request parameter; 
     * if still not found, fallback to default
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param array $directories
     * @return string|false Returns class name on success
     */
    protected function _getController($request, $directories = null)
    {
        if (null === $directories) {
            $directories  = $this->getControllerDirectory();
        }
        if (empty($directories)) {
            throw Zend::exception('Zend_Controller_Dispatcher_Exception', 'Controller directory never set.  Use setControllerDirectory() first');
        }

        $controllerName = $request->getControllerName();
        if (empty($controllerName)) {
            $controllerName = $this->getDefaultController();
            $request->setControllerName($controllerName);
        }

        $className = $this->formatControllerName($controllerName);

        /**
         * Determine if controller is dispatchable
         *
         * Checks to see if a module name is present in the request; if so, 
         * checks for class file existing in module directory. Otherwise, loops through 
         * directories in FIFO order to find it.
         */
        $dispatchable = false;
        $module = (string) $request->getParam('module', false);
        if ($module && isset($directories[$module])) {
            $dispatchable = Zend::isReadable($directories[$module] . DIRECTORY_SEPARATOR . $className . '.php');
            if ($dispatchable) {
                $this->_curDirectory = $directories[$module];
            }
        } else {
            foreach ($directories as $directory) {
                $dispatchable = Zend::isReadable($directory . DIRECTORY_SEPARATOR . $className . '.php');
                if ($dispatchable) {
                    $this->_curDirectory = $directory;
                    break;
                }
            }
        }

        return $dispatchable ? $className : false;
    }

    /**
     * Determine the action name
     *
     * First attempt to retrieve from request; then from request params 
     * using action key; default to default action
     *
     * Returns formatted action name
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     */
    protected function _getAction($request)
    {
        $action = $request->getActionName();
        if (empty($action)) {
            $action = $this->getDefaultAction();
            $request->setActionName($action);
        }

        return $this->formatActionName($action);
    }

    /**
     * Return a class name prefixed with the module name, if a module was specified
     * 
     * @param Zend_Controller_Request_Abstract $request 
     * @param string $className 
     * @return string
     */
    protected function _getModuleClass(Zend_Controller_Request_Abstract $request, $className)
    {
        if (false !== ($module = $request->getParam('module', false))) {
            $className = $this->_formatName($module) . '_' . $className;
        }

        return $className;
    }

    /**
     * Dispatcher class loader
     *
     * Allows loading prefixed classes from a specific directory; used when 
     * modules are utilized.
     * 
     * @param string $class 
     * @param string $dir 
     * @return false|string Returns false if unable to load class, class name 
     * of class loaded otherwise
     */
    public function loadClass($class, $dir)
    {
        if (class_exists($class, false)) {
            return $class;
        }

        $path = str_replace('_', DIRECTORY_SEPARATOR, $class);
        if (strstr($path, DIRECTORY_SEPARATOR)) {
            $file = substr($path, strrpos($path, DIRECTORY_SEPARATOR));
            $spec = $dir . DIRECTORY_SEPARATOR . $file . '.php';
            if (is_readable($spec)) {
                include_once $spec;
                if (!class_exists($class)) {
                    while (strstr($class, '_')) {
                        $class = substr($class, strpos($class, '_'));
                        if (class_exists($class)) {
                            return $class;
                        }
                    }
                    return false;
                }

                return $class;
            }
        } else {
            Zend::loadClass($class, $dir);
            return $class;
        }

        return false;
    }

    /**
     * Return the value of the currently selected dispatch directory (as set by 
     * {@link _getController()})
     * 
     * @return string
     */
    public function getDispatchDirectory()
    {
        return $this->_curDirectory;
    }
}
