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
abstract class Zend_Controller_Request_Abstract
{
    /**
     * Has the action been dispatched?
     * @var boolean 
     */
    protected $_dispatched = false;

    /**
     * Controller key for retrieving controller from params
     * @var string 
     */
    protected $_controllerKey = 'controller';

    /**
     * Action key for retrieving action from params
     * @var string 
     */
    protected $_actionKey = 'action';

    /**
     * Request parameters
     * @var array 
     */
    protected $_params = array();

    /**
     * Retrieve the controller name
     * 
     * @return string
     */
    public function getControllerName()
    {
        return $this->getParam($this->getControllerKey());
    }

    /**
     * Set the controller name to use
     * 
     * @param string $value 
     * @return self
     */
    public function setControllerName($value)
    {
        $this->setParam($this->getControllerKey(), (string) $value);
        return $this;
    }

    /**
     * Retrieve the action name
     * 
     * @return string
     */
    public function getActionName()
    {
        return $this->getParam($this->getActionKey());
    }

    /**
     * Set the action name 
     * 
     * @param string $value 
     * @return self
     */
    public function setActionName($value)
    {
        $this->setParam($this->getActionKey(), (string) $value);
        return $this;
    }

    /**
     * Retrieve the controller key
     * 
     * @return string
     */
    public function getControllerKey() 
    {
        return $this->_controllerKey;
    }

    /**
     * Set the controller key
     * 
     * @param string $key 
     * @return self
     */
    public function setControllerKey($key)
    {
        $this->_controllerKey = (string) $key;
        return $this;
    }

    /**
     * Retrieve the action key
     * 
     * @return string
     */
    public function getActionKey()
    {
        return $this->_actionKey;
    }

    /**
     * Set the action key 
     * 
     * @param string $key 
     * @return self
     */
    public function setActionKey($key)
    {
        $this->_actionKey = (string) $key;
        return $this;
    }

    /**
     * Get an action parameter
     * 
     * @param string $key 
     * @return mixed
     */
    public function getParam($key)
    {
        $key = (string) $key;
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }

        return null;
    }

    /**
     * Set an action parameter
     * 
     * @param string $key 
     * @param mixed $value 
     * @return self
     */
    public function setParam($key, $value)
    {
        $key = (string) $key;
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * Get all action parameters
     * 
     * @return array
     */
     public function getParams()
     {
         return $this->_params;
     }

    /**
     * Set action parameters en masse; does not overwrite
     * 
     * @param array $array 
     * @return self
     */
    public function setParams(array $array)
    {
        $this->_params = $this->_params + (array) $array;
        return $this;
    }

    /**
     * Set flag indicating whether or not request has been dispatched
     * 
     * @param boolean $flag 
     * @return self
     */
    public function setDispatched($flag = true)
    {
        $this->_dispatched = $flag ? true : false;
        return $this;
    }

    /**
     * Determine if the request has been dispatched
     * 
     * @return boolean
     */
    public function isDispatched()
    {
        return $this->_dispatched;
    }
}
