<?php

/** Zend_Http_Request */
require_once 'Zend/Http/Request.php';

/** Zend_Controller_Request_Interface */
require_once 'Zend/Controller/Request/Interface.php';

/**
 * Zend_Controller_Request_Http
 *
 * HTTP request object for use with Zend_Controller family.
 *
 * @uses Zend_Http_Request
 * @uses Zend_Controller_Request_Interface
 * @package Zend_Controller
 * @subpackage Request
 */
class Zend_Controller_Request_Http extends Zend_Http_Request implements Zend_Controller_Request_Interface
{
    /**
     * Current controller
     * @var string 
     */
    protected $_controllerName = null;

    /**
     * Current action
     * @var string 
     */
    protected $_actionName = null;

    /**
     * Dispatch status of request
     * @var boolean 
     */
    protected $_dispatched = false;

    /**
     * Get current controller
     * 
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }
 
    /**
     * Set controller
     * 
     * @param string $controller 
     * @return void
     */
    public function setControllerName($controller)
    {
        $this->_controllerName = (string) $controller;
    }

    /**
     * Get current action
     * 
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }
 
    /**
     * Set action
     * 
     * @param string $action 
     * @return void
     */
    public function setActionName($action)
    {
        $this->_actionName = (string) $action;
    }
 
    /**
     * Set dispatched flag for current action
     * 
     * @param boolean $flag 
     * @return void
     */
    public function setDispatched($flag = true)
    {
        $this->_dispatched = $flag ? true : false;
    }
 
    /**
     * Determine dispatch status of current action
     * 
     * @return boolean
     */
    public function isDispatched()
    {
        return $this->_dispatched;
    }
}
