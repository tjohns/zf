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
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_TimeSync_Protocol
{
    protected $_socket;
    
    /**
     * Connect to the specified NTP server. If called when the socket is
     * already connected, it disconnects and connects again.
     *
     * @param string  $server  IP address or host name.
     * @param integer $port    port number.
     *
     * @access protected
     * @return boolean
     */
    protected function _connect()
    {
        if (is_resource($this->_socket)) {
            @fclose($this->_socket);
            $this->_socket = null;
        }
        
        $socket = fsockopen($this->_timeserver, $this->_port, $errno, $errstr, 5);
        if (!$socket) {
            return false;
        }
                
        $this->_socket = $socket;
        
        return true;
    }
    
    /**
     * Disconnects from the peer, closes the socket.
     *
     * @access protected
     * @return bool
     */
    protected function _disconnect()
    {
        if (!is_resource($this->_socket)) {
            return false;
        }
        
        @fclose($this->_socket);
        $this->_socket = null;
        
        return true;
    }
}
