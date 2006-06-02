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
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Interface for sending eMails through different
 * ways of transport
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Mail_Transport_Interface {
    /**
     * Send an eMail independent from the used transport
     *
     * $headers is an array of headers. For most implementations, these should
     * be constructed into a string using the following algorithm:
     *
     * <code>
     * $final = '';
     * foreach ($headers as $header) {
     *     $final .= $header[0] . ': ' . $header[1] . Zend_Mime::LINEEND;
     * }
     * </code>
     *
     * @param Zend_Mail $mail
     * @param String $body
     * @param Array $headers
     * @param Array $to
     */
    public function sendMail(Zend_Mail $mail, $body, $headers, $to);
}
