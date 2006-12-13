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
 * @package    Zend_Http
 * @subpackage Client_Adapter
 * @version    $Id$
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Uri/Http.php';
require_once 'Zend/Http/Client/Adapter/Socket.php';

/**
 * A sockets based (fsockopen) adapter class for Zend_Http_Client. Can be used
 * on almost every PHP environment, and does not require any special extensions.
 *
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Client_Adapter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       Add proxy authentication support
 */
class Zend_Http_Client_Adapter_Proxy extends Zend_Http_Client_Adapter_Socket 
{
    /**
     * Connect to the remote server
     * 
     * Will try to connect to the proxy server. If no proxy was set, will
     * fall back to the target server (behave like regular Socket adapter)
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     * @param int     $timeout
     */
    public function connect($host, $port = 80, $secure = false)
    {
    	// If no proxy is set, fall back to Socket adapter
    	if (! $this->config['proxy_host']) return parent::connect($host, $port, $secure);
    	
    	// Go through a proxy - the connection is actually to the proxy server
    	$host = $this->config['proxy_host'];
    	$port = $this->config['proxy_port'];

    	// If we are connected to the wrong host, disconnect first
    	if (($this->connected_to[0] != $host || $this->connected_to[1] != $port)) {
    		if (is_resource($this->socket)) $this->close();
    	}

    	// Now, if we are not connected, connect
    	if (! is_resource($this->socket) || ! $this->config['keepalive']) {
    		$this->socket = @fsockopen($host, $port, $errno, $errstr, (int) $this->config['timeout']);
    		if (! $this->socket) {
    			$this->close();
    			throw Zend::exception('Zend_Http_Client_Adapter_Exception',
    			'Unable to Connect to proxy server ' . $host . ':' . $port . '. Error #' . $errno . ': ' . $errstr);
    		}

    		// Update connected_to
    		$this->connected_to = array($host, $port);
    	}
    }
    
    /**
     * Send request to the remote server
     *
     * @todo  add proxy authentication support
     * @param string $method
     * @param Zend_Uri_Http $uri
     * @param float $http_ver
     * @param array  $headers
     * @param string $body
     * @return string Request as string
     */
    public function write($method, $uri, $http_ver = 1.1, $headers = array(), $body = '')
    {
    	// If no proxy is set, fall back to default Socket adapter
    	if (! $this->config['proxy_host']) return parent::write($method, $uri, $http_ver, $headers, $body);
    	
        // Make sure we're properly connected
        if (! $this->socket)
            throw Zend::exception('Zend_Http_Client_Adapter_Exception', "Trying to write but we are not connected");
        
        $host = $this->config['proxy_host'];
        $port = $this->config['proxy_port'];
                
        if ($this->connected_to[0] != $host || $this->connected_to[1] != $port)
            throw Zend::exception('Zend_Http_Client_Adapter_Exception', "Trying to write but we are connected to the wrong proxy server");

        // Build request headers
        $request = "{$method} {$uri->__toString()} HTTP/{$http_ver}\r\n";
        foreach ($headers as $k => $v) {
            if (is_string($k)) $v = ucfirst($k) . ": $v";
            $request .= "$v\r\n";
        }
        
        // Add the request body
        $request .= "\r\n" . $body;
        
        // Send the request
        if (! fwrite($this->socket, $request)) {
        	throw Zend::exception('Zend_Http_Client_Adapter_Exception', "Error writing request to proxy server");
        }
        
        return $request;
    }

    /**
     * Destructor: make sure the socket is disconnected
     *
     */
    public function __destruct()
    {
        if ($this->socket) $this->close();
    }
}
