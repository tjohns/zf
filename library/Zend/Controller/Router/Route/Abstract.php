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
 * @version    $Id: Route.php 9581 2008-06-01 14:08:03Z martel $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Interface */
require_once 'Zend/Controller/Router/Route/Interface.php';

/**
 * Abstract Route
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
abstract class Zend_Controller_Router_Route_Abstract implements Zend_Controller_Router_Route_Interface
{
    /**
     * Hostname to match
     *
     * @var string
     */
    protected $_host;
    
    /**
     * Whether the host to match is a regex or not
     *
     * @var boolean
     */
    protected $_hostIsRegexp = false;
    
    /**
     * Reverse host for assembling
     *
     * @var string
     */
    protected $_hostReverse;
    
    /**
     * Parameters to match in the host
     *
     * @var array
     */
    protected $_hostParams = array();
    
    /**
     * Current request object
     *
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Set the request object
     * 
     * @param  Zend_Controller_Request_Abstract|null $request
     * @return void
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }
    
    /**
     * Get the request object
     * 
     * @return Zend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        if ($this->_request === null) {
            $this->_request = Zend_Controller_Front::getInstance()->getRequest();
        }
        
        return $this->_request;
    }
    
    /**
     * Retrieve Front Controller
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (class_exists('Zend_Controller_Front', false)) {
            /** @see Zend_Controller_Front */
            require_once 'Zend/Controller/Front.php';
            return Zend_Controller_Front::getInstance();
        }

        // Throw exception in all other cases
        /** @see Zend_Controller_Router_Exception */
        require_once 'Zend/Controller/Router/Exception.php';
        throw new Zend_Controller_Router_Exception('Front controller class has not been loaded');
    }
    
    /**
     * Initiate host matching
     *
     * @param  mixed $host
     * @return void
     */
    protected function _initHostMatch($host)
    {
        if (is_array($host) === true) {
            $this->_hostIsRegexp = true;
            
            if (!isset($host['regex'])) {
                /** @see Zend_Controller_Router_Exception */
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Zend_Controller_Router_Exception('"regex" parameter missing for host route');    
            }
            
            $this->_host = $host['regex'];
            
            if (!isset($host['reverse'])) {
                /** @see Zend_Controller_Router_Exception */
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Zend_Controller_Router_Exception('"reverse" parameter missing for host route');    
            }
            
            $this->_hostReverse = $host['reverse'];
            
            if (isset($host['params']) && is_array($host['params'])) {
                $this->_hostParams = $host['params'];
            } else {
                $this->_hostParams = array();
            }
        } else {
            $this->_host = (string) $host;
        }
    }
    
    /**
     * Evaluate the host match, if host matching was initiated.
     * 
     * Returns an array with matched parameters, else false. 
     * 
     * @return array|false
     */
    protected function _evalHostMatch()
    {
        if ($this->_host === null) {
            return array();
        }
        
        $request = $this->getRequest();
        
        // Strict fail if host is provided but not http request
        if (!$request instanceof Zend_Controller_Request_Http) {
            return false;
        }
        
        $currentHost = $request->getHttpHost();
        if ($this->_hostIsRegexp === false) {
            if ($this->_host !== $currentHost) {
                return false;
            } else {
                return array();
            }
        } else {
            $match = preg_match('#^' . $this->_host . '$#', $currentHost, $params);
            
            if ($match === 0) {
                return false;
            } else {
                $values = array();
                foreach ($params as $num => $param) {
                    if ($num === 0) {
                        continue;
                    }
                    
                    if (!isset($this->_hostParams[$num])) {
                        /** @see Zend_Controller_Router_Exception */
                        require_once 'Zend/Controller/Router/Exception.php';
                        throw new Zend_Controller_Router_Exception('Parameter name for host regex "'
                                                                   . $num . '" was not defined');
                    }
                    
                    $values[$this->_hostParams[$num]] = $param;
                }
                
                return $values;
            }
        }
    }
    
    /**
     * Prepend the hostname, if host matching is active
     *
     * @param  string $route
     * @param  array  $data
     * @return string
     */
    protected function _prependHost($route, $data)
    {
        if ($this->_host === null) {
            return $route;
        }
        
        if ($this->_hostIsRegexp === false) {
            $hostname = $this->_host;
        } else {
            $flippedParams = array_flip($this->_hostParams);
            $hostData      = array_intersect_key($data, $flippedParams);
            
            $hostname = @vsprintf($this->_hostReverse, $hostData);
            if ($hostname === false) {
                /** @see Zend_Controller_Router_Exception */
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Zend_Controller_Router_Exception('Cannot assemble host. Too few arguments?');
            }
        }
        
        $request = $this->getRequest();
        if ($request instanceof Zend_Controller_Request_Http) {
            $url = $request->getScheme() . '://' . $hostname . $this->_prependBase($route);
        } else {
            $url = $this->_prependBase($route);
        }
        
        return $url;
    }
    
    /**
     * Determine if the baseUrl should be prepended, and prepend if necessary
     *
     * @param  string $url
     * @return string
     */
    protected function _prependBase($url)
    {
        $request = $this->getRequest();
        if (!$request instanceof Zend_Controller_Request_Http) {
            return '/' . ltrim($url, '/');
        }
        
        $base = rtrim($request->getBaseUrl(), '/');
        if (!empty($base) && ('/' != $base)) {
            $url = $base . '/' . ltrim($url, '/');
        } else {
            $url = '/' . ltrim($url, '/');
        }

        return $url;
    }
}