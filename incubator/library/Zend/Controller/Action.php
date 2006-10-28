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


/** Zend_Controller_Action_Exception */
require_once 'Zend/Controller/Action/Exception.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Controller_Action
{
    /**
     * Zend_Controller_Request_Abstract object wrapping the request environment
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request = null;

    /**
     * Zend_Controller_Response_Abstract object wrapping the response 
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response = null;

    /**
     * Array of arguments provided to the constructor, minus the 
     * {@link $_request Request object}.
     * @var array 
     */
    protected $_invokeArgs = array();

    /**
     * Class constructor
     *
     * Marked final to ensure that the request object is provided to the 
     * constructor. However, additional construction actions can be invoked in 
     * {@link init()}, and all additional arguments passed to the constructor 
     * will be passed as arguments to init().
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return void
     */
    final public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->_request  = $request;
        $this->_response = $response;

        if (1 < func_num_args()) {
            $argv = func_get_args();
            array_shift($argv);       // strip request
            array_shift($argv);       // strip response

            // Set invocation arguments
            $this->_invokeArgs = $argv;
        }

        $reflection = new ReflectionObject($this);
        $init = $reflection->getMethod('init');
        $init->invokeArgs($this, $this->_invokeArgs);
    }

    /**
     * Initialize object
     *
     * Called from {@link __construct()} with all arguments passed to the 
     * constructor, minus the request object. Use for custom object 
     * initialization.
     * 
     * @return void
     */
    public function init()
    {
    }

    /**
     * Return the Request object
     * 
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set the Request object
     * 
     * @param Zend_Controller_Request_Abstract $request 
     * @return self
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Return the Response object
     * 
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set the Response object
     * 
     * @param Zend_Controller_Response_Abstract $response 
     * @return self
     */
    public function setResponse(Zend_Controller_Response_Abstract $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Return the array of constructor arguments (minus the Request object)
     * 
     * @return array
     */
    public function getInvokeArgs()
    {
        return $this->_invokeArgs;
    }

    /**
     * Pre-dispatch routines
     *
     * Called before action method. If using class with 
     * {@link Zend_Controller_Front}, it may modify the 
     * {@link $_request Request object} and reset its dispatched flag in order 
     * to skip processing the current action.
     * 
     * @return void
     */
    public function preDispatch()
    {
    }

    /**
     * Post-dispatch routines
     *
     * Called after action method execution. If using class with 
     * {@link Zend_Controller_Front}, it may modify the 
     * {@link $_request Request object} and reset its dispatched flag in order 
     * to process an additional action.
     *
     * Common usages for postDispatch() include rendering content in a sitewide 
     * template, link url correction, setting headers, etc.
     * 
     * @return void
     */
    public function postDispatch()
    {
    }

    /**
     * Proxy for undefined methods.  Default behavior is to throw an
     * exception on undefined methods, however this function can be
     * overridden to implement magic (dynamic) actions, or provide run-time 
     * dispatching.
     *
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        if (empty($methodName)) {
            $msg = 'No action specified and no default action has been defined in __call() for '
                 . get_class($this);
        } else {
            $msg = get_class($this) . '::' . $methodName
                 .'() does not exist and was not trapped in __call()';
        }

        throw new Zend_Controller_Action_Exception($msg);
    }

    /**
     * Call the action specified in the request object, and return a response
     *
     * Not used in the Action Controller implementation, but left for usage in 
     * Page Controller implementations. Dispatches a method based on the 
     * request.
     *
     * Returns a Zend_Controller_Response_Abstract object, instantiating one 
     * prior to execution if none exists in the controller.
     *
     * {@link preDispatch()} is called prior to the action, 
     * {@link postDispatch()} is called following it.
     *
     * @param null|Zend_Controller_Request_Abstract $request Optional request 
     * object to use
     * @param null|Zend_Controller_Response_Abstract $response Optional response 
     * object to use
     * @return Zend_Controller_Response_Abstract
     */
    public function run(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null)
    {
        if (null !== $request) {
            $this->setRequest($request);
        }

        if (null !== $response) {
            $this->setResponse($response);
        }

        $this->preDispatch();

        $action = $this->getRequest()->getActionName();
        if (null === $action) {
            $action = 'noRoute';
        }
        $action = $action . 'Action';

        $this->{$action}();

        $this->postDispatch();

        return $this->getResponse();
    }

    /**
     * Gets a parameter from the {@link $_request Request object}.  If the
     * parameter does not exist, NULL will be return.
     *
     * If the parameter does not exist and $default is set, then
     * $default will be returned instead of NULL.
     *
     * @param string $paramName
     * @param mixed $default
     * @return boolean
     */
    final protected function _getParam($paramName, $default = null)
    {
        $value = $this->_request->getParam($paramName);
        if ((null == $value) && (null !== $default)) {
            $value = $default;
        }

        return $value;
    }


    /**
     * Return all parameters in the {@link $_request Request object}
     * as an associative array.
     *
     * @return array
     */
    final protected function _getAllParams()
    {
        return $this->_request->getParams();
    }


    /**
     * Forward to another controller/action.
     *
     * It is important to supply the unformatted names, i.e. "article"
     * rather than "ArticleController".  The dispatcher will do the
     * appropriate formatting when the request is received.
     *
     * @param string $controllerName
     * @param string $actionName
     * @param array $params
     * @return void
     */
    final protected function _forward($controllerName, $actionName, $params=array())
    {
        $this->_request->setControllerName($controllerName)
                       ->setActionName($actionName)
                       ->setParams($params)
                       ->setDispatched(false);
    }


    /**
     * Redirect to another URL
     *
     * @param string $url
     */
    final protected function _redirect($url)
    {
        if (headers_sent($file, $line)) {
            throw new Zend_Controller_Action_Exception('Cannot redirect because headers were already been sent in file ' . $file . ', line ' . $line);
        }

        // prevent header injections
        $url = str_replace(array("\n", "\r"), '', $url);

        // redirect
        header("Location: $url");
        exit();
    }
}
