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


/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Controller_Action
{
    /**
     * Zend_Controller_Dispatcher_Token object wrapping this controller/action call.
     * @var Zend_Controller_Dispatcher_Token
     */
    protected $_action = null;

    /**
     * Parameters, copied from Zend_Controller_Dispatcher_Token object
     * @var array
     */
    private $_params = null;

    /**
     * Zend_Controller_Dispatcher_Token object wrapping the controller/action for the next
     * call.  This is set by Zend_Controller_Action::_forward().
     * @var Zend_Controller_Dispatcher_Token
     */
    private $_nextAction = null;


    /**
     * Any controller extending Zend_Controller_Action must provide an index()
     * method.  The index() method is the default action for the controller
     * when no action is specified.
     *
     * This only handles a controller which has been called with no action
     * specified in the URI.
     *
     * For handling nonexistant actions in controllers (bad action part of URI),
     * the controller class must provide a __call() method or an exception
     * will be thrown.
     */
    abstract public function indexAction();


    /**
     * Class constructor
     */
    public function __construct()
    {}


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
            $msg = 'No action specified and no default action has been defined in __call() for '.get_class($this);
        } else {
            $msg = get_class($this).'::'.$methodName.'() does not exist and was not trapped in __call().';
        }

        throw new Zend_Controller_Action_Exception($msg);
    }


    /**
     * Initialize the class instance variables and then call the action.
     *
     * @param Zend_Controller_Dispatcher_Token $action
     */
    final public function run(Zend_Controller_Dispatcher_Interface $dispatcher,
                              Zend_Controller_Dispatcher_Token    $action)
    {
        $this->_action     = $action;
        $this->_params     = $action->getParams();

        if (!strlen( $action->getActionName() )) {
            $action->setActionName('index');
        }

        $methodName = $dispatcher->formatActionName($action->getActionName());

        if (!method_exists($this, $methodName)) {
            $this->__call($methodName, array());
        } else {
            $method = new ReflectionMethod($this, $methodName);
            if ($method->isPublic() && !$method->isStatic()) {
                $this->{$methodName}();
            } else {
                throw new Zend_Controller_Action_Exception('Illegal action called.');
            }
        }

        $nextAction = $this->_nextAction;
        $this->_nextAction = null;
        return $nextAction;
    }


    /**
     * Gets a parameter that was passed to this controller.  If the
     * parameter does not exist, FALSE will be return.
     *
     * If the parameter does not exist and $default is set, then
     * $default will be returned instead of FALSE.
     *
     * @param string $paramName
     * @param string $default
     * @return boolean
     */
    final protected function _getParam($paramName, $default=null)
    {
        if (array_key_exists($paramName, $this->_params)) {
            return $this->_params[$paramName];
        }

        if ($default===null) {
            return false;
        } else {
            return $default;
        }
    }


    /**
     * Return all parameters that were passed to the controller
     * as an associative array.
     *
     * @return array
     */
    final protected function _getAllParams()
    {
        return $this->_params;
    }


    /**
     * Forward to another controller/action.
     *
     * It is important to supply the unformatted names, i.e. "article"
     * rather than "ArticleController".  The dispatcher will do the
     * appropriate formatting when the Zend_Controller_Dispatcher_Token item is received.
     *
     * @param string $controllerName
     * @param string $actionName
     * @param array $params
     */
    final protected function _forward($controllerName, $actionName, $params=array())
    {
        $this->_nextAction = new Zend_Controller_Dispatcher_Token($controllerName, $actionName, $params);
    }


    /**
     * Redirect to another URL
     *
     * @param string $url
     */
    final protected function _redirect($url)
    {
        if (headers_sent()) {
            throw new Zend_Controller_Action_Exception('Cannot redirect because headers have already been sent.');
        }

        // prevent header injections
        $url = str_replace(array("\n", "\r"), '', $url);

        // redirect
        header("Location: $url");
        exit();
    }
}