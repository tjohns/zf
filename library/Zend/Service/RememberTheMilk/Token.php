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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk_Token
{
    /**
     * Token string
     *
     * @var string
     */
    protected $_token;

    /**
     * User associated with the token
     *
     * @var Zend_Service_RememberTheMilk_Contact
     */
    protected $_user;

    /**
     * Permissions identifier
     *
     * @var string
     */
    protected $_perms;

    /**
     * Constructor to initialize the object with data
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $data = $data->auth;
        $this->_token = $data->token;
        $this->_perms = $data->perms;
        $this->_user = new Zend_Service_RememberTheMilk_Contact($data->user);
    }

    /**
     * Returns the token string.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Returns the user associated with the token.
     *
     * @return Zend_Service_RememberTheMilk_Contact
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Returns the permissions identifier associated with the token.
     *
     * @see Zend_Service_RememberTheMilk::PERMS_READ
     * @see Zend_Service_RememberTheMilk::PERMS_WRITE
     * @see Zend_Service_RememberTheMilk::PERMS_DELETE
     * @return string
     */
    public function getPerms()
    {
        return $this->_perms;
    }
}
