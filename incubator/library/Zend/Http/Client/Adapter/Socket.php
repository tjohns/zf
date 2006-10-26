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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Uri/Http.php';
require_once 'Zend/Http/Client/Adapter/Interface.php';
require_once 'Zend/Http/Client/Adapter/Exception.php';

/**
 * A sockets based (fsockopen) adapter class for Zend_Http_Client. Can be used
 * on almost every PHP environment, and does not require any special extensions.
 *
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Client_Adapter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_Client_Adapter_Socket implements Zend_Http_Client_Adapter_Interface 
{
    /**
     * The socket for server connection
     *
     * @var resource|null
     */
    protected $socket = null;
    
    /**
     * What host/port are we connected to?
     *
     * @var array
     */
    protected $connected_to = array(null, null);
    
    /**
     * Parameters array
     *
     * @var array
     */
    protected $config = array();
    
    /**
     * Adapter constructor, currently empty. Config is set using setConfig()
     *
     */
    public function __construct() 
    {
    }
    
    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array()) 
    {
        if (! is_array($config))
            throw new Zend_Http_Client_Adapter_Exception('$config expects an array, ' . gettype($config) . ' recieved.');
            
        foreach ($config as $k => $v) {
            $this->config[strtolower($k)] = $v;
        }
    }
    
    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     * @param int     $timeout
     */
    public function connect($host, $port = 80, $secure = false)
    {
        // If we're already connected, disconnect first
        if ($this->socket) $this->close();
        
        // If the URI should be accessed via SSL, prepend the Hostname with ssl://
        $host = ($secure ? 'ssl://' . $host : $host);
        
        // If we are connected to a different server or port, disconnect first
        if ($this->socket && is_array($this->connected_to) && 
            ($this->connected_to[0] != $host || $this->connected_to[1] != $port))
                $this->close();
        
        // Do the actual connection
        $this->socket = @fsockopen($host, $port, $errno, $errstr, (int) $this->config['timeout']);
        if (! $this->socket) {
            $this->close();
            throw new Zend_Http_Client_Adapter_Exception('Unable to Connect to ' . 
                $host . ':' . $port . '. Error #' . $errno . ': ' . $errstr);
        }
        
        // Update connected_to
        $this->connected_to = array($host, $port);
    }
    
    /**
     * Send request to the remote server
     *
     * @param string $method
     * @param Zend_Uri_Http $uri
     * @param float $http_ver
     * @param array  $headers
     * @param string $body
     */
    public function write($method, $uri, $http_ver = 1.1, $headers = array(), $body = '')
    {
        // Make sure we're properly connected
        if (! $this->socket) 
            throw new Zend_Http_Client_Adapter_Exception("Trying to write but we are not connected");
        
        $host = $uri->getHost();
            $host = (strtolower($uri->getScheme()) == 'https' ? 'ssl://' . $host : $host);
        if ($this->connected_to[0] != $host || $this->connected_to[1] != $uri->getPort())
            throw new Zend_Http_Client_Adapter_Exception("Trying to write but we are connected to the wrong host");

            // Build request headers
        $request = "{$method} {$uri->__toString()} HTTP/{$http_ver}\r\n";
        foreach ($headers as $k => $v) {
            if (is_string($k)) $v = ucfirst($k) . ": $v";
            $request .= "$v\r\n";
        }
        
        // Add the request body
        $request .= "\r\n" . $body;
        
        // Send the request
        fwrite($this->socket, $request);
    }
    
    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        $response = '';
        while ($buff = fread($this->socket, 8192)) {
            $response .= $buff;
        }
        
        return $response;
    }
    
    /**
     * Close the connection to the server
     *
     */
    public function close()
    {
        fclose($this->socket);
        $this->socket = null;
        $this->connected_to = array(null, null);
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
