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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Route.php 1847 2006-11-23 11:36:41Z martel $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Abstract */
require_once 'Zend/Controller/Router/Route/Abstract.php';

/**
 * StaticRoute is used for managing static URIs.
 *
 * It's a lot faster compared to the standard Route implementation.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Router_Route_Static extends Zend_Controller_Router_Route_Abstract
{

    protected $_route = null;
    protected $_defaults = array();

    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        $route = ($config->route instanceof Zend_Config)    ? $config->route->toArray()    : $config->route;
        $defs  = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($route, $defs);
    }

    /**
     * Prepares the route for mapping.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     */
    public function __construct($route, $defaults = array())
    {
        $host = null;
        if (is_array($route)) {
            if (!isset($route['path'])) {
                /** @see Zend_Controller_Router_Exception */
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Zend_Controller_Router_Exception('$route array must contain a "path" element');
            }
            
            if (isset($route['host'])) {
                $host = $route['host'];
            }
            
            $route = $route['path'];
        }
        
        $this->_route    = trim($route, '/');
        $this->_defaults = (array) $defaults;
        
        if ($host !== null) {
            $this->_initHostMatch($host);
        }
    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $hostResult = $this->_evalHostMatch();
        if ($hostResult === false) {
            return false;
        }
        
        if (trim($path, '/') == $this->_route) {
            return array_merge($this->_defaults, $hostResult);
        }
        return false;
    }

    /**
     * Assembles a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array())
    {
        $route = $this->_prependHost($this->_route, array_merge($this->_defaults, $data));
        return $route;
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

}