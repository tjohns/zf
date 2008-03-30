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
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk_Request
{
    /**
     * API method to call
     *
     * @var string
     */
    protected $_method;

    /**
     * Whether or not the method to call requires authentication
     *
     * @var bool
     */
    protected $_auth;

    /**
     * Whether or not the method requires a timeline
     *
     * @var bool
     */
    protected $_timeline;

    /**
     * Parameters to include in the method call
     */
    protected $_params;

    /**
     * Constructor to initialize the object with data
     *
     * @return void
     */
    public function __construct()
    {
        $this->_params = array();
        $this->_auth = true;
        $this->_timeline = true;
    }

    /**
     * Sets the API method to be called.
     *
     * @param string $method Method name
     */
    public function setMethod($method)
    {
        $this->_method = $method;
    }

    /**
     * Returns the API method to be called.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Sets whether or not the API method to be called requires
     * authentication.
     *
     * @param bool $flag TRUE if authentication is required, FALSE otherwise,
     *                   defaults to TRUE (optional)
     */
    public function useAuth($flag = true)
    {
        $this->_auth = $flag;
    }

    /**
     * Returns whether or not the API method to be called requires
     * authentication.
     *
     * @return bool TRUE if authentication is required, FALSE otherwise
     */
    public function requiresAuth()
    {
        return $this->_auth;
    }

    /**
     * Sets whether or not the API method to be called requires a timeline.
     *
     * @param bool $flag TRUE if a timeline is required, FALSE otherwise,
     *                   defaults to TRUE (optional)
     */
    public function useTimeline($flag = true)
    {
        $this->_timeline = $flag;
    }

    /**
     * Returns whether or not the API method to be called requires a
     * timeline.
     *
     * @return bool TRUE if a timeline is required, FALSE otherwise
     */
    public function requiresTimeline()
    {
        return $this->_timeline;
    }

    /**
     * Adds a parameter to the request.
     *
     * @param string $name Name of the parameter
     * @param string $value Value of the parameter
     */
    public function addParameter($name, $value)
    {
        $this->_params[$name] = $value;
    }

    /**
     * Returns the parameters for the request.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_params;
    }
}