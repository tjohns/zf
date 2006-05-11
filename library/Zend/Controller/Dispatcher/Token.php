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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Dispatcher_Token
{
    /**
     * Name of the controller to dispatch.  This is the raw name, such as
     * it would be extracted from the URI.  It is the responsibility of the
     * dispatcher to format it.
     * @var string
     */
    protected $_controller = null;

    /**
     * Name of the action to dispatch.  The action is usually a method of
     * the controller.  This is the raw name, such as it would be extracted
     * from the URI.  It is the responsibility of the dispatcher to format it.
     * @var string
     */
	protected $_action     = null;

	/**
	 * Array of key/value pairs to pass as parameters to the controller.
	 * @var array
	 */
	protected $_params     = array();


	/**
	 * Class constructor.  A Zend_Controller_Dispatcher_Token object must be built with a controller
	 * name and an action, but parameters are optional.
	 *
	 * @param string $controllerName
	 * @param string $actionName
	 * @param array $params
	 */
	public function __construct($controllerName, $actionName, $params=array())
	{
	    $this->_controller = $controllerName;
	    $this->_action     = $actionName;
	    $this->_params     = $params;
	}


	/**
	 * Sets the controller name.  This is the raw name, such as it would
	 * be extracted from the URI.  It is the responsibility of the dispatcher
	 * to format it.
	 *
	 * @param string $controllerName
	 * @return Zend_Controller_Dispatcher_Token
	 */
	public function setControllerName($controllerName)
	{
	    $this->_controller = $controllerName;
	    return $this;
	}


	/**
	 * Returns the controller name, in the raw form.
	 *
	 * @return string
	 */
	public function getControllerName()
	{
	    return $this->_controller;
	}


	/**
	 * Returns the action name, in the raw form.
	 *
	 * @return string
	 */
	public function getActionName()
	{
	    return $this->_action;
	}


	/**
	 * Sets the action name.  This is the raw name, such as it would be
	 * extracted from the URI.  It is the responsibility of the dispatcher
	 * to format it.
	 *
	 * @param string $actionName
	 * @return Zend_Controller_Dispatcher_Token
	 */
	public function setActionName($actionName)
	{
	    $this->_action = $actionName;
	    return $this;
	}


	/**
	 * Get the parameters array.
	 *
	 * @return array
	 */
    public function getParams()
    {
       return $this->_params;
    }


    /**
     * Sets the parameters array.
     *
     * @param string $paramsArray
     * @return Zend_Controller_Dispatcher_Token
     */
    public function setParams($paramsArray)
    {
        if (!is_array($paramsArray)) {
            throw new Zend_Controller_Dispatcher_Exception('Parameters must be set as an array.');
        }
        $this->_params = $paramsArray;
        return $this;
    }
}

