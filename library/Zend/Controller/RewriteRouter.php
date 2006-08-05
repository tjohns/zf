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

    protected $_rewriteBase = null;
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
        if (is_null($config->{$section})) {
            throw new Exception("No route configuration in section '{$section}'");
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

    public function setRewriteBase($value)
    {
        $this->_rewriteBase = (string) $value;
    }

    public function detectRewriteBase()
    {
        $base = '';
        if (empty($_SERVER['PATH_INFO'])) $base = $_SERVER['REQUEST_URI'];
        else if ($pos = strpos($_SERVER['REQUEST_URI'], $_SERVER['PATH_INFO'])) {
            $base = substr($_SERVER['REQUEST_URI'], 0, $pos);
        }
        return rtrim($base, '/');
    }
    
    public function getRewriteBase()
    {
        if ($this->_rewriteBase === null) {
            $this->_rewriteBase = $this->detectRewriteBase();
        }
        return $this->_rewriteBase;
    }

    public function route(Zend_Controller_Dispatcher_Interface $dispatcher)
    {
        /** @todo Replace with Zend_Request object */
        $path = $_SERVER['REQUEST_URI'];
        if (strstr($path, '?')) {
            $path = substr($path, 0, strpos($path, '?'));
        }

        /** Remove RewriteBase */
        $rb = $this->getRewriteBase();
        if (strlen($rb) > 0 && strpos($path, $rb) === 0) {
            $path = substr($path, strlen($rb));
        }

        /** Find the matching route */
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

        $token = new Zend_Controller_Dispatcher_Token($controller, $action, $params);

        if (!$dispatcher->isDispatchable($token)) {
            throw new Zend_Controller_Router_Exception('Request could not be mapped to a route.');
        } else {
            return $token;
        }

    }

}
