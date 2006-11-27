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
 * Zend_Auth_Adapter
 */
require_once 'Zend/Auth/Adapter.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Digest_Adapter extends Zend_Auth_Adapter
{
    /**
     * Creates a new digest authentication object against the $filename provided
     *
     * @param  string $filename
     * @throws Zend_Auth_Digest_Exception
     */
    public function __construct($filename)
    {}

    /**
     * Authenticates against the given parameters
     *
     * @param  string $filename
     * @param  string $username
     * @param  string $password
     * @param  string $realm
     * @return Zend_Auth_Digest_Token
     */
    public static function staticAuthenticate($filename, $realm, $username, $password)
    {}

    /**
     * Authenticates the realm, username and password given
     *
     * @param  string $realm
     * @param  string $username
     * @param  string $password
     * @throws Zend_Auth_Digest_Exception
     * @return Zend_Auth_Digest_Token
     */
    public function authenticate($realm, $username, $password)
    {}

}
