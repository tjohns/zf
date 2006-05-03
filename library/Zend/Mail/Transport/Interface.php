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
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Interface for sending eMails through different
 * ways of transport
 *
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Mail_Transport_Interface {
    /**
     * Send an eMail independent from the used transport
     *
     * @param Zend_Mail $mail
     * @param String $body
     * @param String $headers
     */
    public function sendMail(Zend_Mail $mail, $body, $header);
}
