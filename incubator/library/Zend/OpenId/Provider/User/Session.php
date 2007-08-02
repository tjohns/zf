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
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Provider
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * @see Zend_OpenId_Provider_User
 */
require_once "Zend/OpenId/Provider/User.php";

/**
 * Class to get/store information about logged in user in Web Browser using
 * PHP session
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Provider
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_OpenId_Provider_User_Session extends Zend_OpenId_Provider_User
{

    /**
     * Stores information about logged in user in session data
     *
     * @param string $id user identity URL
     * @return void
     */
    public function setLoggedInUser($id)
    {
        if (session_id() === "") {
            session_start();
        }
        $_SESSION['openid.logged_in'] = $id;
    }

    /**
     * Returns identity URL of logged in user or false
     *
     * @return mixed
     */
    public function getLoggedInUser()
    {
        if (session_id() === "") {
            session_start();
        }
        if (isset($_SESSION['openid.logged_in'])) {
            return $_SESSION['openid.logged_in'];
        }
        return false;
    }

    /**
     * Performs logout. Clears information about logged in user.
     *
     * @return void
     */
    public function delLoggedInUser()
    {
        if (session_id() === "") {
            session_start();
        }
        unset($_SESSION['openid.logged_in']);
    }

}
