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
    /**
     * Holds the current socket connection
     *
     * @var array
     */
    protected $_socket;
    
    /**
     * Exceptions that might have occured
     *
     * @var array
     */
    protected $_exceptions;
    
    /**
     * Hostname for timeserver
     *
     * @var string
     */
    protected $_timeserver;
    
    /**
     * Port number for this timeserver
     *
     * @var int
     */
    protected $_port;
    
    /**
     * Abstract method that writes/receives data to/from the timeserver
     * 
     * @return int unix timestamp
     */
    abstract protected function _query();
    
    /**
     * Connect to the specified timeserver. If called when the socket is
     * already connected, it disconnects and connects again.
     * 
     * @return void
     */
    protected function _connect()
    {
        if (is_resource($this->_socket)) {
            @fclose($this->_socket);
            $this->_socket = null;
        }
        
        $socket = @fsockopen($this->_timeserver, $this->_port, $errno, $errstr, Zend_TimeSync::$options['timeout']);
        if (!$socket) {
            throw Zend::exception(
                'Zend_TimeSync_ProtocolException', 
                "could not connect to '$this->_timeserver' on port '$this->_port', reason: '$errstr'"
            );
        }
        
        $this->_socket = $socket;
    }
    
    /**
     * Disconnects from the peer, closes the socket.
     * 
     * @return void
     */
    protected function _disconnect()
    {
        if (!is_resource($this->_socket)) {
            throw Zend::exception(
                'Zend_TimeSync_ProtocolException', 
                "could not close server connection from '$this->_timeserver' on port '$this->_port'"
            );
        }
        
        @fclose($this->_socket);
        $this->_socket = null;
    }
    
    /**
     * Query this timeserver without using the fallback mechanism
     * 
     * @param   $locale optional locale
     * @return  Zend_Date
     * @throws  Zend_TimeSync_Exception
     */
    public function getDate($locale = false)
    {
        $timestamp = $this->_query();
        
        return new Zend_Date($timestamp, Zend_Date::TIMESTAMP, $locale);
    }
    
    /**
     * Return the exceptions that have occured while trying to write/receive data
     * to/from the current timeserver
     * 
     * @return  mixed
     */
    public function getExceptions()
    {
        if (isset($this->_exceptions)) {
            return $this->_exceptions;
        }
        
        return false;
    }
    
    /**
     * Stores an internal exception, used by the facade Zend_TimeSync
     * 
     * @param   $exception
     * @return  void
     */
    public function addException(Zend_TimeSync_ProtocolException $exception)
    {
        $this->_exceptions[] = $exception;
    }
}
