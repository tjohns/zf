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
interface Zend_Controller_Request_Interface
{
    /**
     * Retrieve the controller name
     * 
     * @return string
     */
    public function getControllerName();

    /**
     * Set the controller name to use
     * 
     * @param string $value 
     * @return void
     */
    public function setControllerName($value);

    /**
     * Retrieve the action name
     * 
     * @return string
     */
    public function getActionName();

    /**
     * Set the action name 
     * 
     * @param string $value 
     * @return void
     */
    public function setActionName($value);

    /**
     * Retrieve the controller key
     * 
     * @return string
     */
    public function getControllerKey();

    /**
     * Set the controller key
     * 
     * @param string $key 
     * @return void
     */
    public function setControllerKey($key);

    /**
     * Retrieve the action key
     * 
     * @return string
     */
    public function getActionKey();

    /**
     * Set the action key 
     * 
     * @param string $key 
     * @return void
     */
    public function setActionKey($key);

    /**
     * Get an action parameter
     * 
     * @param string $key 
     * @return mixed
     */
    public function getParam($key);

    /**
     * Set an action parameter
     * 
     * @param string $key 
     * @param mixed $value 
     * @return void
     */
    public function setParam($key, $value);

    /**
     * Get all action parameters
     * 
     * @return array
     */
    public function getParams();

    /**
     * Set action parameters en masse; does not overwrite
     * 
     * @param array $array 
     * @return void
     */
    public function setParams($array);

    /**
     * Set flag indicating whether or not request has been dispatched
     * 
     * @param boolean $flag 
     * @return void
     */
    public function setDispatched($flag = true);

    /**
     * Determine if the request has been dispatched
     * 
     * @return boolean
     */
    public function isDispatched();
}
