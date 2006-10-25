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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/** Zend_Controller_Router_Interface */
require_once 'Zend/Controller/Router/Interface.php';

/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Exception.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Request_Http */
require_once 'Zend/Controller/Request/Http.php';

/** Zend_Controller_Route */
require_once 'Zend/Controller/Router/Route.php';

/**
 * Ruby routing based Router.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_RewriteRouter implements Zend_Controller_Router_Interface
{

    /**
     * Array of invocation parameters to use when instantiating action 
     * controllers
     * @var array 
     */
    protected $_invokeParams = array();
    protected $_routes = array();
    protected $_currentRoute = null;

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
        $this->addDefaultRoutes();
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


    public function addDefaultRoutes()
    {
        // Add default route (for root url - '/')
        $default = new Zend_Controller_Router_Route('', array('controller' => 'index', 'action' => 'index'));
        $this->addRoute('default', $default);
        
        // Route for Router v1 compatibility
        $compat = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'));
        $this->addRoute('compat', $compat); 
    }

    public function addRoute($name, Zend_Controller_Router_Route_Interface $route) {
        $this->_routes[$name] = $route;
    }

    public function addRoutes($routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }
    }

    public function addConfig(Zend_Config $config, $section) 
    {
        if ($config->{$section} === null) {
            throw new Zend_Controller_Router_Exception("No route configuration in section '{$section}'");
        }
        foreach ($config->{$section} as $name => $info) {
            $reqs = (isset($info->reqs)) ? $info->reqs->asArray() : null;
            $defs = (isset($info->defaults)) ? $info->defaults->asArray() : null;
            $this->addRoute($name, new Zend_Controller_Router_Route($info->route, $defs, $reqs));
        }
    }

    public function getRoute($name)
    {
        if (!isset($this->_routes[$name])) {
            throw new Zend_Controller_Router_Exception("Route $name is not defined");
        }
        return $this->_routes[$name];
    }

    public function getCurrentRoute()
    {
        if (!isset($this->_currentRoute)) {
            throw new Zend_Controller_Router_Exception("Current route is not defined");
        }
        return $this->_currentRoute;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }

    public function route(Zend_Controller_Request_Abstract $request)
    {
        
        if (!$request instanceof Zend_Controller_Request_Http) {
            throw new Zend_Controller_Router_Exception('Zend_Controller_RewriteRouter requires a Zend_Controller_Request_Http-based request object');
        }

        $pathInfo = $request->getPathInfo();

        /** Find the matching route */
        foreach (array_reverse($this->_routes) as $route) {
            if ($params = $route->match($pathInfo)) {
                foreach ($params as $param => $value) {
                    $request->setParam($param, $value);
                }
                $this->_currentRoute = $route;
                break;
            }
        }
        
        return $request;

    }

}
