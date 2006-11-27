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
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth
{
    /**
     * Authentication adapter
     *
     * @var Zend_Auth_Adapter
     */
    protected $_adapter;

    /**
     * Sets the authentication adapter
     *
     * @param  Zend_Auth_Adapter $adapter
     * @return void
     */
    public function __construct(Zend_Auth_Adapter $adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * Authenticates against the attached adapter
     *
     * If and only if $useSession is true, then the authentication token is saved to
     * the PHP session.
     *
     * All other parameters are passed along to the adapter's authenticate() method.
     *
     * @param  boolean $useSession
     * @return Zend_Auth_Token_Interface
     */
    public function authenticate($useSession = true)
    {
        $args = func_get_args();
        $args = array_slice($args, 1);
        $token = call_user_func_array(array($this->_adapter, __METHOD__), $args);

        /**
         * @todo persist token in session if $useSession === true
         */

        return $token;
    }

}
