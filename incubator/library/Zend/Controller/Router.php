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
 * @subpackage Router
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Controller_Router_Interface */
require_once 'Zend/Controller/Router/Interface.php';

/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Exception.php';

/** Zend_Controller_Request_Interface */
require_once 'Zend/Controller/Request/Interface.php';

/** Zend_Controller_Request_Http */
require_once 'Zend/Controller/Request/Http.php';


/**
 * Simple first implementation of a router, to be replaced
 * with rules-based URI processor.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Router implements Zend_Controller_Router_Interface
{
    /**
     * Array of invocation parameters to use when instantiating action 
     * controllers
     * @var array 
     */
    protected $_invokeParams = array();

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
     * Route a request
     * 
     * @param Zend_Controller_Request_Interface $request 
     * @return void
     */
    public function route(Zend_Controller_Request_Interface $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            throw new Zend_Controller_Router_Exception('Zend_Controller_Router requires a Zend_Controller_Request_Http-based request object');
        }

        $pathInfo = '';
        $pathSegs = array();
        if ($request instanceof Zend_Controller_Request_Http) {
            $pathInfo = $request->getPathInfo();
            $pathSegs = explode('/', $pathInfo);
        }

        // Get controller from request
        if (null === $request->getControllerName()) {
            $controllerKey = $request->getControllerKey();
            $controller    = $request->getParam($key);
            if (null === $controller) {
                // Attempt to get from path_info; controller is first item
                $pathInfo = $request->getPathInfo();
                $pathSegs = explode('/', $pathInfo);
                if (isset($pathSegs[0])) {
                    $controller = $pathSegs[0];
                }
            } elseif (null === $controller) {
                // Default: 'index' controller
                $controller = 'index';
            }

            // Set request controller
            $request->setControllerName($controller);
        }

        // Get action from request
        if (null === $request->getActionName()) {
            // get action from action parameter, if available
            $actionKey = $request->getActionKey();
            $action    = $request->getParam($key);
            if (null === $action) {
                // Attempt to get from path_info; action is second item
                if (isset($pathSegs[1])) {
                    $action = $pathSegs[1];
                }
            } elseif (null === $action) {
                // Default: 'noRoute' action
                $action = 'noRoute';
            }

            // Set request action
            $request->setActionName($action);
        }

        /**
         * Any optional parameters after the action are stored in
         * an array of key/value pairs:
         *
         * http://www.zend.com/controller-name/action-name/param-1/3/param-2/7
         *
         * $params = array(2) {
         *              ["param-1"]=> string(1) "3"
         *              ["param-2"]=> string(1) "7"
         * }
         */
        $params = array();
        if (2 < count($pathSegs)) {
            for ($i=2; $i < sizeof($pathSegs); $i = $i+2) {
                $params[$pathSegs[$i]] = isset($pathSegs[$i+1]) ? $pathSegs[$i+1] : null;
            }
        }
        $request->setParams($params);

        return $request;
    }
}
