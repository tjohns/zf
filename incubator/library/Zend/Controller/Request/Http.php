<?php

/** Zend_Http_Request */
require_once 'Zend/Http/Request.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/**
 * Zend_Controller_Request_Http
 *
 * HTTP request object for use with Zend_Controller family.
 *
 * @uses Zend_Http_Request
 * @uses Zend_Controller_Request_Abstract
 * @package Zend_Controller
 * @subpackage Request
 */
class Zend_Controller_Request_Http extends Zend_Controller_Request_Abstract
{
    /**
     * Zend_Http_Request object
     * @var Zend_Http_Request 
     */
    protected $_httpRequest = null;

    /**
     * ReflectionObject version of $_httpRequest
     * @var ReflectionObject
     */
    protected $_httpRequestReflection;

    /**
     * Constructor
     *
     * Instantiates a Zend_Http_Request and assigns it to {@link $_httpRequest}
     * 
     * @param null|string|Zend_Uri $uri 
     * @return void
     */
    public function __construct($uri = null)
    {
        $this->_httpRequest = new Zend_Http_Request($uri);
    }

    /**
     * Overload and proxy to Zend_Http_Request object
     * 
     * @param string $key 
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_httpRequest->__get($key);
    }

    /**
     * Overload and proxy to Zend_Http_Request object
     * 
     * @param string $key 
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        return $this->_httpRequest->__set($key, $value);
    }

    /**
     * Overload and proxy to Zend_Http_Request object
     * 
     * @param string $methodName
     * @param array $args
     * @return mixed
     */
    public function __call($methodName, $args)
    {
        if (method_exists($this->_httpRequest, $methodName)) {
            if (!isset($this->_httpRequestReflection)) {
                $this->_httpRequestReflection = new ReflectionObject($this->_httpRequest);
            }
            return $this->_httpRequestReflection->getMethod($methodName)->invokeArgs($this->_httpRequest, $args);
        }

        throw new Zend_Controller_Request_Exception('Method "' . $methodName . '" does not exist');
    }
}
