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

/** Zend_Controller_Dispatcher_Exception */
require_once 'Zend/Controller/Dispatcher/Exception.php';

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
     * Directory where Zend_Controller_Action files are stored.
     * @var string
     */
    protected $_directory = null;

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
    public function __construct()
    {
        if (0 < func_num_args()) {
            $argv = func_get_args();
            $this->setParams($argv);
        }
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
     * @param string $unformatted
     * @return string
     */
	public function formatActionName($unformatted)
	{
        $formatted = $this->_formatName($unformatted);
	    return strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . 'Action';
	}


    /**
     * Formats a string from a URI into a PHP-friendly name.  Replaces words
     * separated by "-" or "." with camelCaps, title-cases words separated by 
     * underscores,  and removes any characters that are not alphanumeric.
     *
     * @param string $unformatted
     * @return string
     */
    protected function _formatName($unformatted)
    {
        $unformatted = str_replace(array('-', '.'), ' ', strtolower($unformatted));
        $unformatted = preg_replace('[^a-z0-9_ ]', '', $unformatted);
        $unformatted = str_replace(' ', '', ucwords($unformatted));

        $unformatted = str_replace('_', ' ', $unformatted);
        return str_replace(' ', '_', ucwords($unformatted));
    }


    /**
     * Sets the directory where the Zend_Controller_Action class files are stored.
     *
     * @param string $dir
     */
    public function setControllerDirectory($dir)
    {
        if (!is_dir($dir) or !is_readable($dir)) {
            throw new Zend_Controller_Dispatcher_Exception("Directory \"$dir\" not found or not readable.");
        }

        $this->_directory = rtrim($dir, '/\\');
    }

    /**
     * Return the currently set directory for Zend_Controller_Action class 
     * lookup
     * 
     * @return string
     */
    public function getControllerDirectory()
    {
        return $this->_directory;
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be dispatched to a controller.
     * This only verifies that the Zend_Controller_Action can be dispatched and does not
     * guarantee that the action will be accepted by the Zend_Controller_Action.
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return unknown
     */
	public function isDispatchable(Zend_Controller_Request_Abstract $request)
	{
        if ($request->isDispatched()) {
            return false;
        }

        return $this->_dispatch($request, false);
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
     * @return void
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = $params;
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
     * Set response object to pass to action controllers
     * 
     * @param Zend_Controller_Response_Abstract|null $response 
     * @return void
     */
    public function setResponse(Zend_Controller_Response_Abstract $response = null)
    {
        $this->_response = $response;
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
	 * Dispatch to a controller/action
	 *
	 * @param Zend_Controller_Request_Abstract $action
	 * @return boolean
	 */
	public function dispatch(Zend_Controller_Request_Abstract $request)
	{
	    return $this->_dispatch($request);
	}


	/**
	 * If $performDispatch is FALSE, this method will check if a controller
	 * file exists.  This still doesn't necessarily mean that it can be dispatched
	 * in the stricted sense, as file may not contain the controller class or the
	 * controller may reject the action.
	 *
	 * If $performDispatch is TRUE, then this method will actually
	 * instantiate the controller and call its action.  Calling the action
	 * is done by passing a Zend_Controller_Dispatcher_Token to the controller's constructor.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param boolean $performDispatch
	 * @return void
	 */
	protected function _dispatch(Zend_Controller_Request_Abstract $request, $performDispatch = true)
	{
        // Controller directory check
	    if ($this->_directory === null) {
	        throw new Zend_Controller_Dispatcher_Exception('Controller directory never set.  Use setControllerDirectory() first');
	    }

        // Get controller class name
	    $className  = $this->formatControllerName($request->getControllerName());

	    /**
	     * If $performDispatch is FALSE, only determine if the controller file
	     * can be accessed.
	     */
	    if (!$performDispatch) {
	        return Zend::isReadable($this->_directory . DIRECTORY_SEPARATOR . $className . '.php');
	    }

        // Load the class file
        Zend::loadClass($className, $this->_directory);

        // Perform reflection on the class and verify it's a controller
        $reflection = new ReflectionClass($className);
        if (!$reflection->isSubclassOf(new ReflectionClass('Zend_Controller_Action'))) {
           throw new Zend_Controller_Dispatcher_Exception("Controller \"$className\" is not an instance of Zend_Controller_Action");
        }

        // Get any instance arguments and instantiate a controller object
        $args = $this->getParams();

        // Prepend response object, if available
        if (null !== ($response = $this->getResponse())) {
            array_unshift($args, $response);
        }

        // prepend request object
        array_unshift($args, $request);

        $controller = $reflection->newInstanceArgs($args);

        // Determine the action name; default to noRoute if none specified in 
        // the request object, or __call() if the method does not exist
        $invokeArgs = array();
        if (null !== ($action = $request->getActionName())) {
            $action = $this->formatActionName($request->getActionName());
        } else {
            $action = $this->formatActionName('noRoute');
        }

        if (!$reflection->hasMethod($action)) {
            array_push($invokeArgs, $action);
            $action == '__call';
        }
        $method = $reflection->getMethod($action);

        // Dispatch the method call
        $request->setDispatched(true);
        $controller->preDispatch();
        if ($request->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            $method->invokeArgs($controller, $invokeArgs);
            $controller->postDispatch();
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
        $reflection = null;
        $method     = null;
	}
}
