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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 

/** Zend_Controller_Dispatcher_Abstract */
require_once 'Zend/Controller/Dispatcher/Abstract.php';

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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Dispatcher extends Zend_Controller_Dispatcher_Abstract
{
    /**
     * Convert a class name to a filename
     * 
     * @param string $class 
     * @return string
     */
    public function classToFilename($class)
    {
        return str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be 
     * dispatched to a controller.
     *
     * Use this method wisely. By default, the dispatcher will fall back to the 
     * default controller (either in the module specified or the global default) 
     * if a given controller does not exist. This method returning false does 
     * not necessarily indicate the dispatcher will not still dispatch the call 
     * to the default controller.
     *
     * If no controller is set in the request, this method will return true, as 
     * the dispatcher will always use the default controller in such an 
     * instance.
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     */
    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        $className = $this->_getController($request);
        if (!$className) {
            return true; // no controller specified; this will dispatch to the default controller
        }

        $fileSpec = $this->classToFilename($className);

        // Test for controller in controller directories
        $found = false;
        foreach ($this->getControllerDirectory() as $dir) {
            $test = $dir . DIRECTORY_SEPARATOR . $fileSpec;
            if (Zend::isReadable($test)) {
                $found = true;
                break;
            }
        }

        return $found;
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
        return $this;
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
        return $this;
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
     * By default, if a controller is not dispatchable, dispatch() will throw 
     * an exception. If you wish to use the default controller instead, set the 
     * param 'useDefaultControllerAlways' via {@link setParam()}.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return boolean
     * @throws Zend_Controller_Dispatcher_Exception
     */
    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);

        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)) {
            if (!$this->getParam('useDefaultControllerAlways')) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $request->getControllerName() . ')');
            }

            $className = $this->_getDefaultControllerName($request);
        } else {
            $className = $this->_getController($request);
            if (!$className) {
                $className = $this->_getDefaultControllerName($request);
            }
        }

        /**
         * Load the controller class file
         */
        $className = $this->loadClass($className);
        
        /**
         * Instantiate controller with request, response, and invocation 
         * arguments; throw exception if it's not an action controller
         */
        $controller = new $className($request, $this->getResponse(), $this->getParams());
        if (!$controller instanceof Zend_Controller_Action) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception("Controller '$className' is not an instance of Zend_Controller_Action");
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
         * Dispatch the method call, bookending with pre/postDispatch() calls
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
     * Load a controller class
     * 
     * Attempts to load the controller class file from {@link getControllerDirectory()}.
     *
     * @param string $className 
     * @return void
     */
    public function loadClass($className)
    {
        Zend::loadClass($className, $this->getControllerDirectory());
        return $className;
    }

    /**
     * Get controller name
     *
     * Try request first; if not found, try pulling from request parameter; 
     * if still not found, fallback to default
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string|false Returns class name on success
     */
    protected function _getController($request)
    {
        $controllerName = $request->getControllerName();
        if (empty($controllerName)) {
            return false;
        }

        return $this->formatControllerName($controllerName);
    }

    /**
     * Retrieve default controller
     *
     * Retrieve the default controller as a class name. Sets the request 
     * object's controller, and unsets the action.
     *
     * @param Zend_Controller_Request_Abstract $request 
     * @return string
     */
    protected function _getDefaultControllerName($request)
    {
        $controller = $this->getDefaultController();
        $request->setControllerName($controller)
                ->setActionName(null);

        return $this->formatControllerName($controller);
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
}
