<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** Zend_Controller_Router_Interface */
require_once 'Zend/Controller/Router/Interface.php';

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Exception.php';

/** Zend_Controller_Dispatcher_Token */
require_once 'Zend/Controller/Dispatcher/Token.php';

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

    protected $_rewriteBase = '';
    protected $_routes = array();
    protected $_currentRoute = null;

    public function __construct()
    {
        // Add default route (for root url - '/')
        $default = new Zend_Controller_Router_Route('', array('controller' => 'index', 'action' => 'index'));
        $this->addRoute('default', $default);
        
        // Route for Router v1 compatibility
        $compat = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'));
        $this->addRoute('compat', $compat); 
        
        // Set magic default of RewriteBase:
        $filename = basename($_SERVER['SCRIPT_FILENAME']);
        $base = $_SERVER['SCRIPT_NAME'];
        if (strpos($_SERVER['REQUEST_URI'], $filename) === false) {
            // Default of '' for cases when SCRIPT_NAME doesn't contain a filename (ZF-205)
            $base = (strpos($base, $filename) !== false) ? dirname($base) : '';
        }
        $this->_rewriteBase = rtrim($base, '/\\');
    }

    public function addRoute($name, Zend_Controller_Router_Route_Interface $route) {
        $this->_routes[$name] = $route;
    }

    public function addRoutes($routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }
    }

    public function getRoute($name)
    {
        if (!isset($this->_routes[$name]))
            throw new Zend_Controller_Router_Exception("Route $name is not defined");
        return $this->_routes[$name];
    }

    public function getCurrentRoute()
    {
        if (!isset($this->_currentRoute))
            throw new Zend_Controller_Router_Exception("Current route is not defined");
        return $this->_currentRoute;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }

    public function setRewriteBase($value)
    {
        $this->_rewriteBase = (string) $value;
    }

    public function getRewriteBase()
    {
        return $this->_rewriteBase;
    }

    public function route(Zend_Controller_Dispatcher_Interface $dispatcher)
    {
        /**
         * @todo Replace with Zend_Request object
         */
        $path = $_SERVER['REQUEST_URI'];
        if (strstr($path, '?')) {
            $path = substr($path, 0, strpos($path, '?'));
        }

        // Remove RewriteBase
        if (strlen($this->_rewriteBase) > 0 && strpos($path, $this->_rewriteBase) === 0) {
            $path = substr($path, strlen($this->_rewriteBase));
        }

        /**
         * Find the matching route
         */
        $controller = 'index';
        $action = 'noRoute';
        
        foreach (array_reverse($this->_routes) as $route) {
            if ($params = $route->match($path)) {
                $controller = $params['controller'];
                $action     = $params['action'];
                $this->_currentRoute = $route;
                break;
            }
        }

        $actionObj = new Zend_Controller_Dispatcher_Token($controller, $action, $params);

        if (!$dispatcher->isDispatchable($actionObj)) {
            throw new Zend_Controller_Router_Exception('Request could not be mapped to a route.');
        } else {
            return $actionObj;
        }

    }

}
