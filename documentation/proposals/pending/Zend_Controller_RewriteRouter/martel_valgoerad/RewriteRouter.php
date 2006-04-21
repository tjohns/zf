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
 * @author     Michael Minicki aka Martel Valgoerad (martel@post.pl)
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_RewriteRouter implements Zend_Controller_Router_Interface
{
    
    private $_routes = array();
    
    public function __construct()
    {
        // add sane default
        $this->addRoute(':controller/:action', array('controller' => 'index', 'action' => 'index'));
    }
    
    public function addRoute($map, $params = array(), $reqs = array()) 
    {
        array_unshift($this->_routes, new Zend_Controller_Router_Route($map, $params, $reqs));
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
        
        /**
         * Find the matching route
         */
        foreach ($this->_routes as $route) {
            if ($params = $route->match($path)) {
                $controller = $params['controller'];
                $action     = $params['action'];
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
