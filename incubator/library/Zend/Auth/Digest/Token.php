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
 * Zend_Auth_Token_Interface
 */
require_once 'Zend/Auth/Token/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Digest_Token implements Zend_Auth_Token_Interface
{
    /**
     * Defined by Zend_Auth_Token_Interface
     *
     * @return boolean
     */
    public function isValid()
    {}

    /**
     * Defined by Zend_Auth_Token_Interface
     *
     * @return string|null
     */
    public function getMessage()
    {}

    /**
     * Defined by Zend_Auth_Token_Interface
     *
     * @return unknown_type
     */
    public function getIdentity()
    {}

    /**
     * Defined by Zend_Auth_Token_Interface
     *
     * @param  unknown_type $identity
     */
    public function setIdentity($identity)
    {}
}
