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

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

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
     * @param Zend_Controller_Request_Abstract $request 
     * @return void
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            throw new Zend_Controller_Router_Exception('Zend_Controller_Router requires a Zend_Controller_Request_Http-based request object');
        }

        $pathInfo = $request->getPathInfo();
        $pathSegs = explode('/', trim($pathInfo, '/'));

        /**
         * Get controller and action from request
         * Attempt to get from path_info; controller is first item, action 
         * second
         */
        if (isset($pathSegs[0])) {
            $controller = array_shift($pathSegs);
        }
        if (isset($pathSegs[0])) {
            $action = array_shift($pathSegs);
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
        $segs = count($pathSegs);
        if (0 < $segs) {
            for ($i = 0; $i < $segs; $i = $i + 2) {
                $key = urldecode($pathSegs[$i]);
                $value = isset($pathSegs[$i+1]) ? urldecode($pathSegs[$i+1]) : null;
                $params[$key] = $value;
            }
        }
        $request->setParams($params);

        /**
         * Set controller and action, now that params are set
         */
        if (isset($controller)) {
            $request->setControllerName(urldecode($controller));
        }

        if (isset($action)) {
            $request->setActionName(urldecode($action));
        }

        return $request;
    }
}
