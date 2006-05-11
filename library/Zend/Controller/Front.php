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


/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Router_Interface */
require_once 'Zend/Controller/Router/Interface.php';

/** Zend_Controller_Plugin_Broker */
require_once 'Zend/Controller/Plugin/Broker.php';


/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Front
{
    /**
     * Instance of Zend_Controller_Front
     * @var Zend_Controller_Front
     */
    static private $_instance = null;

    /**
     * Instance of Zend_Controller_RouterInterface
     * @var Zend_Controller_RouterInterface
     */
    private $_router = null;

    /**
     * Instance of Zend_Controller_DispatcherInterface
     * @var Zend_Controller_DispatcherInterface
     */
    private $_dispatcher = null;

    /**
     * Instance of Zend_Controller_Plugin_Broker
     * @var Zend_Controller_Plugin_Broker
     */
    private $_plugins = null;


	/**
	 * Singleton pattern
	 *
	 * Instantiate the plugin broker.
	 */
	private function __construct()
	{
	    $this->_plugins = new Zend_Controller_Plugin_Broker();
	}

	/**
	 * Singleton pattern
	 */
	private function __clone()
	{}


	/**
	 * Return one and only one instance of the Zend_Controller_Front object
	 *
	 * @return Zend_Controller_Front
	 */
	static public function getInstance()
	{
        if (!self::$_instance instanceof self) {
           self::$_instance = new self();
        }

        return self::$_instance;
	}


	/**
	 * Convenience feature, calls getInstance()->setControllerDirectory()->dispatch()
	 *
	 * @param string $controllerDirectory
	 */
    static public function run($controllerDirectory)
	{
		self::getInstance()
					->setControllerDirectory($controllerDirectory)
					->dispatch();
	}


	/**
	 * Convenience method, passthru to Zend_Controller_Dispatcher::setControllerDirectory()
	 *
	 * @param string $directory
	 */
	public function setControllerDirectory($directory)
	{
        $dispatcher = $this->getDispatcher();
        if (!method_exists($dispatcher, 'setControllerDirectory')) {
           throw new Zend_Controller_Front_Exception('Custom dispatcher does not support setting controller directory.');
        }
	   $dispatcher->setControllerDirectory($directory);
	   return $this;
	}


	/**
	 * Set the router object.  The router is responsible for mapping
	 * the request to a Zend_Controller_Dispatcher_Token object for dispatch.
	 *
	 * @param Zend_Controller_RouterInterface $router
	 */
	public function setRouter(Zend_Controller_Router_Interface $router)
	{
	    $this->_router = $router;
	}


	/**
	 * Return the router object.
	 *
	 * @return Zend_Controller_RouterInterface
	 */
	public function getRouter()
	{
	    /**
	     * Instantiate the default router if one was not set.
	     */
        if (!$this->_router instanceof Zend_Controller_Router_Interface) {
            require_once 'Zend/Controller/Router.php';
            $this->_router = new Zend_Controller_Router();
        }
        return $this->_router;
	}


	/**
	 * Set the dispatcher object.  The dispatcher is responsible for
	 * taking a Zend_Controller_Dispatcher_Token object, instantiating the controller, and
	 * call the action method of the controller.
	 *
	 * @param Zend_Controller_DispatcherInterface $dispatcher
	 * @return Zend_Controller_Front
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
	 * Register a plugin.
	 *
	 * @param Zend_Controller_Plugin_Interface $plugin
	 * @return Zend_Controller_Front
	 */
	public function registerPlugin(Zend_Controller_Plugin_Interface $plugin)
	{
	    $this->_plugins->registerPlugin($plugin);
	    return $this;
	}


	/**
	 * Unregister a plugin.
	 *
	 * @return Zend_Controller_Front
	 */
    public function unregisterPlugin(Zend_Controller_Plugin_Interface $plugin)
    {
        $this->_plugins->unregisterPlugin($plugin);
        return $this;
    }


	/**
	 * Dispatch an HTTP request to a controller/action.
	 */
	public function dispatch()
	{
	    /* @var $action Zend_Controller_Dispatcher_Token */

	    try {
	        // notify plugins that the router is startup up
            $this->_plugins->routeStartup();

    	    /**
    	     * Route a URI to a controller/action.  If the route cannot be
    	     * made, an exception is thrown.
    	     */
    	    
    	    try {
                $action = $this->getRouter()->route($this->getDispatcher());
    	    } catch(Zend_Controller_Router_Exception $e) {
    	        // Failed routing (basically a 404, no controller found)
    	        // change the action
    	        $action = new Zend_Controller_Dispatcher_Token('index', 'noRoute', array('error' => $e));
    	    }

            // notify plugins that the router is shutting down
            $action = $this->_plugins->routeShutdown($action);

            // notify plugins that the dispatch loop is starting up
            $action = $this->_plugins->dispatchLoopStartup($action);

            /**
             * Attempt to dispatch to the controller/action.  On return, either
             * false will be given to indicate completion, or Zend_Controller_Dispatcher_Token will be
             * given to indicate a forward to another controller/action must
             * be performed.
             */
            while ($action instanceof Zend_Controller_Dispatcher_Token) {
                // notify plugins that a dispatch is about to occur
                $action = $this->_plugins->preDispatch($action);
                
                $action = $this->getDispatcher()->dispatch($action);
                
                // notify plugins that the dispatch has finish
                $action = $this->_plugins->postDispatch($action);
            }

            // notify plugins that the dispatch loop is shutting down
            $this->_plugins->dispatchLoopShutdown();
	    } catch (Exception $e) {
	        // @todo exception processing
	        //echo('EXCEPTION: ' . $e->getMessage());
		throw $e;
	    }
	}
}