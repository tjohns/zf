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

/** Zend_Controller_Request_Interface */
require_once 'Zend/Controller/Request/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Controller_Action
{
    /**
     * Zend_Controller_Request_Interface object wrapping the request environment
     * @var Zend_Controller_Request_Interface
     */
    protected $_request = null;

    /**
     * Array of arguments provided to the constructor, minus the 
     * {@link $_reqeuest Request object}.
     * @var array 
     */
    protected $_invokeArgs = array();

    /**
     * Any controller extending Zend_Controller_Action must provide a noRouteAction()
     * method.  The noRouteAction() method is the default action for the controller
     * when no action is specified.
     *
     * This only handles a controller which has been called with no action
     * specified in the URI.
     *
     * For handling nonexistant actions in controllers (bad action part of URI),
     * the controller class must provide a __call() method or an exception
     * will be thrown.
     */
    abstract public function noRouteAction();


    /**
     * Class constructor
     *
     * Marked final to ensure that the request object is provided to the 
     * constructor. However, additional construction actions can be invoked in 
     * {@link init()}, and all additional arguments passed to the constructor 
     * will be passed as arguments to init().
     *
     * @param Zend_Controller_Request_Interface
     * @return void
     */
    final public function __construct(Zend_Controller_Request_Interface $request)
    {
        $this->_request = $request;

        if (1 < func_num_args()) {
            $argv = func_get_args();
            array_shift($argv);       // strip request
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
     * @return Zend_Controller_Request_Interface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set the Request object
     * 
     * @param Zend_Controller_Request_Interface $request 
     * @return void
     */
    public function setRequest(Zend_Controller_Request_Interface $request)
    {
        $this->_request = $request;
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
     * overrided to implement magic (dynamic) actions.
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
     * Initialize the class instance variables and then call the action.
     *
     * Not used in the Action Controller implementation, but left for usage in 
     * Page Controller implementations. Dispatches a method based on the 
     * request; if no $request is provided, a Zend_Controller_Http_Request is 
     * instantiated.
     *
     * {@link preDispatch()} is called prior to the action, 
     * {@link postDispatch()} is called following it.
     *
     * @param Zend_Controller_Request_Interface $request
     */
    public function run(Zend_Controller_Request_Interface $request = null)
    {
        if (null === $request) {
            require_once 'Zend/Controller/Request/Http.php';
            $request = new Zend_Controller_Request_Http();
        }

        $this->preDispatch();
        $action = $request->getActionName();
        if (null === $action) {
            $action = 'noRoute';
        }
        $action = $action . 'Action';
        $this->{$action}();
        $this->postDispatch();
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
        $this->_request->setControllerName($controllerName);
        $this->_request->setActionName($actionName);
        $this->_request->setParams($params);
        $this->_request->setDispatched(false);
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
