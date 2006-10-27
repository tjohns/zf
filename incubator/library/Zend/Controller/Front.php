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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Controller_Plugin_Broker */
require_once 'Zend/Controller/Plugin/Broker.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Router_Interface */
require_once 'Zend/Controller/Router/Interface.php';

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Front
{
    /**
     * Instance of Zend_Controller_Plugin_Broker
     * @var Zend_Controller_Plugin_Broker
     */
    private $_plugins = null;

    /**
     * Instance of Zend_Controller_Request_Abstract
     * @var Zend_Controller_Request_Abstract
     */
    private $_request = null;

    /**
     * Instance of Zend_Controller_Router_Interface
     * @var Zend_Controller_Router_Interface
     */
    private $_router = null;

    /**
     * Instance of Zend_Controller_Dispatcher_Interface
     * @var Zend_Controller_Dispatcher_Interface
     */
    private $_dispatcher = null;

    /**
     * Instance of Zend_Controller_Response_Abstract
     * @var Zend_Controller_Response_Abstract
     */
    private $_response = null;

    /**
     * Array of invocation parameters to use when instantiating action
     * controllers
     * @var array
     */
    protected $_invokeParams = array();

	/**
	 * Constructor
	 *
	 * Instantiate the plugin broker.
	 */
	public function __construct()
	{
	    $this->_plugins = new Zend_Controller_Plugin_Broker();
	}

	/**
	 * Convenience feature, calls setControllerDirectory()->setRouter()->dispatch()
	 *
	 * @param string $controllerDirectory
	 */
    static public function run($controllerDirectory)
	{
        require_once 'Zend/Controller/Router.php';
        $frontController = new self();
		echo $frontController
            ->setControllerDirectory($controllerDirectory)
            ->setRouter(new Zend_Controller_Router())
            ->dispatch();
	}

	/**
	 * Convenience method, passthru to Zend_Controller_Dispatcher::setControllerDirectory()
	 *
	 * @param string $directory
	 * @return self
	 */
	public function setControllerDirectory($directory)
	{
        $dispatcher = $this->getDispatcher();
        if (!method_exists($dispatcher, 'setControllerDirectory')) {
           throw new Zend_Controller_Front_Exception('Custom dispatcher does not support setting controller directory');
        }
	   $dispatcher->setControllerDirectory($directory);
	   return $this;
	}

    /**
     * Convenience method, passthru to Zend_Controller_Dispatcher::getControllerDirectory()
     *
     * @return string
     */
    public function getControllerDirectory()
    {
        $dispatcher = $this->getDispatcher();
        if (!method_exists($dispatcher, 'getControllerDirectory')) {
           throw new Zend_Controller_Front_Exception('Custom dispatcher does not support setting controller directory');
        }
	   return $dispatcher->getControllerDirectory();
    }

    /**
     * Set the default controller (unformatted string)
     *
     * @param string $controller
     * @return self
     */
    public function setDefaultController($controller)
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->setDefaultController($controller);
        return $this;
    }

    /**
     * Retrieve the default controller (unformatted string)
     *
     * @return string
     */
    public function getDefaultController()
    {
        return $this->getDispatcher()->getDefaultController();
    }

    /**
     * Set the default action (unformatted string)
     *
     * @param string $action
     * @return self
     */
    public function setDefaultAction($action)
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->setDefaultAction($action);
        return $this;
    }

    /**
     * Retrieve the default action (unformatted string)
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->getDispatcher()->getDefaultAction();
    }

    /**
     * Set request class/object
     *
	 * Set the request object.  The request holds the request environment.
     *
     * If a class name is provided, it will instantiate it
     *
     * @param string|Zend_Controller_Request_Abstract $request
     * @throws Zend_Controller_Front_Exception if invalid request class
     * @return self
     */
    public function setRequest($request)
    {
        if (is_string($request)) {
            Zend::loadClass($request);
            $request = new $request();
        }
        if (!$request instanceof Zend_Controller_Request_Abstract) {
            throw new Zend_Controller_Front_Exception('Invalid request class');
        }

        $this->_request = $request;

        return $this;
    }

	/**
	 * Return the request object.
	 *
	 * @return null|Zend_Controller_Request_Abstract
	 */
	public function getRequest()
	{
        return $this->_request;
	}

    /**
     * Set router class/object
     *
	 * Set the router object.  The router is responsible for mapping
	 * the request to a controller and action.
     *
     * If a class name is provided, instantiates router with any parameters
     * registered via {@link addParam()} or {@link setParams()}.
     *
     * @param string|Zend_Controller_Router_Interface $router
     * @throws Zend_Controller_Front_Exception if invalid router class
     * @return self
     */
    public function setRouter($router)
    {
        if (is_string($router)) {
            Zend::loadClass($router);
            $reflection = new ReflectionClass($router);
            $router = $reflection->newInstanceArgs($this->getParams());
        }
        if (!$router instanceof Zend_Controller_Router_Interface) {
            throw new Zend_Controller_Front_Exception('Invalid router class');
        }

        $this->_router = $router;

        return $this;
    }

	/**
	 * Return the router object.
	 *
	 * @return null|Zend_Controller_Router_Interface
	 */
	public function getRouter()
	{
        return $this->_router;
	}

	/**
	 * Set the dispatcher object.  The dispatcher is responsible for
	 * taking a Zend_Controller_Dispatcher_Token object, instantiating the controller, and
	 * call the action method of the controller.
	 *
	 * @param Zend_Controller_Dispatcher_Interface $dispatcher
	 * @return self
	 */
	public function setDispatcher(Zend_Controller_Dispatcher_Interface $dispatcher)
	{
	    $this->_dispatcher = $dispatcher;
	    return $this;
	}

	/**
	 * Return the dispatcher object.
	 *
	 * @return Zend_Controller_DispatcherInteface
	 */
	public function getDispatcher()
	{
	    /**
	     * Instantiate the default dispatcher if one was not set.
	     */
        if (!$this->_dispatcher instanceof Zend_Controller_Dispatcher_Interface) {
            require_once 'Zend/Controller/Dispatcher.php';
            $this->_dispatcher = new Zend_Controller_Dispatcher();
        }
        return $this->_dispatcher;
	}

    /**
     * Set response class/object
     *
	 * Set the response object.  The response is a container for action
     * responses and headers. Usage is optional.
     *
     * If a class name is provided, instantiates a response object.
     *
     * @param string|Zend_Controller_Response_Abstract $response
     * @throws Zend_Controller_Front_Exception if invalid response class
     * @return self
     */
    public function setResponse($response)
    {
        if (is_string($response)) {
            Zend::loadClass($response);
            $response = new $response();
        }
        if (!$response instanceof Zend_Controller_Response_Abstract) {
            throw new Zend_Controller_Front_Exception('Invalid response class');
        }

        $this->_response = $response;

        return $this;
    }

	/**
	 * Return the response object.
	 *
	 * @return null|Zend_Controller_Response_Abstract
	 */
	public function getResponse()
	{
        return $this->_response;
	}

    /**
     * Add a parameter to use when instantiating an action controller
     *
     * @param mixed $param
     * @return void
     */
    public function addParam($param)
    {
        array_push($this->_invokeParams, $param);
    }

    /**
     * Set parameters to pass to action controller constructors
     *
     * @param array $params
     * @return self
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = $params;
        return $this;
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
	 * Register a plugin.
	 *
	 * @param Zend_Controller_Plugin_Abstract $plugin
	 * @return self
	 */
	public function registerPlugin(Zend_Controller_Plugin_Abstract $plugin)
	{
	    $this->_plugins->registerPlugin($plugin);
	    return $this;
	}

	/**
	 * Unregister a plugin.
	 *
	 * @param Zend_Controller_Plugin_Abstract $plugin
	 * @return self
	 */
    public function unregisterPlugin(Zend_Controller_Plugin_Abstract $plugin)
    {
        $this->_plugins->unregisterPlugin($plugin);
        return $this;
    }

	/**
	 * Dispatch an HTTP request to a controller/action.
     *
     * @param Zend_Controller_Request_Abstract|null $request
     * @param Zend_Controller_Response_Abstract|null $response
     * @return Zend_Controller_Response_Abstract
	 */
	public function dispatch(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null)
	{
        /**
         * Instantiate default request object (HTTP version) if none provided
         */
        if ((null === $request) && (null === ($request = $this->getRequest()))) {
            Zend::loadClass('Zend_Controller_Request_Http');
            $request = new Zend_Controller_Request_Http();
        }

        /**
         * Instantiate default response object (HTTP version) if none provided
         */
        if ((null === $response) && (null === ($response = $this->getResponse()))) {
            Zend::loadClass('Zend_Controller_Response_Http');
            $response = new Zend_Controller_Response_Http();
        }

        /**
         * Register request and response objects with plugin broker
         */
        $this->_plugins
            ->setRequest($request)
            ->setResponse($response);

        // Begin dispatch
	    try {
            /**
             * Route request to controller/action, if a router is provided
             */
            if (null !== ($router = $this->getRouter())) {
                /**
                * Notify plugins of router startup
                */
                $this->_plugins->routeStartup($request);

                $router->setParams($this->getParams());
                $router->route($request);

                /**
                * Notify plugins of router completion
                */
                $this->_plugins->routeShutdown($request);
            }

            /**
             * Notify plugins of dispatch loop startup
             */
            $this->_plugins->dispatchLoopStartup($request);

            $dispatcher = $this->getDispatcher();
            $dispatcher->setParams($this->getParams());

            /**
             *  Attempt to dispatch the controller/action. If the $request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 */
                $this->_plugins->preDispatch($request);

                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                if (!$request->isDispatched()) {
                    continue;
                }

                /**
                 * Dispatch request
                 */
                $dispatcher->dispatch($request, $response);

                /**
                 * Notify plugins of dispatch completion
                 */
                $this->_plugins->postDispatch($request);
            } while (!$request->isDispatched());


            /**
             * Notify plugins of dispatch loop completion
             */
            $this->_plugins->dispatchLoopShutdown();
	    } catch (Exception $e) {
            $response->setException($e);
	    }

        return $response;
	}
}
