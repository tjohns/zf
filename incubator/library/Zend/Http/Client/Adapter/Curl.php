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
 * An adapter class for Zend_Http_Client based on the curl extension. 
 * Curl requires libcurl. See for full requirements the PHP manual: http://php.net/curl
 *
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Client_Adapter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_Client_Adapter_Curl implements Zend_Http_Client_Adapter_Interface
{
    /**
	 * The curl session handle
	 *
	 * @var resource|null
	 */
    protected $curl = null;

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
	 * Response gotten from server
	 *
	 * @var string
	 */
    private $response = null;

    /**
     * Boolean to know if the connection should be secure
     *
     * @var boolean
     */
    private $is_secure;
    
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
	 * Initialize curl
	 *
	 * @param string  $host
	 * @param int     $port
	 * @param boolean $secure
	 */
    public function connect($host, $port = 80, $secure = false)
    {
        // If we're already connected, disconnect first
        if ($this->curl) $this->close();

        // Set is_secure option for usage in write();
        $this->is_secure = $secure;

        // If we are connected to a different server or port, disconnect first
        if ($this->curl && is_array($this->connected_to) &&
            ($this->connected_to[0] != $host || $this->connected_to[1] != $port))
        $this->close();

        // Do the actual connection
        $this->curl = curl_init();
        if ($port != 80) {
            curl_setopt($this->curl, CURLOPT_PORT, intval($port));
        }
        
        // Set timeout
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->config['timeout']);
        
        if (! $this->curl) {
            $this->close();
            throw new Zend_Http_Client_Adapter_Exception('Unable to Connect to ' .
            $host . ':' . $port);
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
        // set URL
        curl_setopt($this->curl, CURLOPT_URL, $uri->__toString());
        // Make sure we're properly connected
        if (! $this->curl)
            throw new Zend_Http_Client_Adapter_Exception("Trying to write but we are not connected");

        if ($this->connected_to[0] != $uri->getHost() || $this->connected_to[1] != $uri->getPort())
            throw new Zend_Http_Client_Adapter_Exception("Trying to write but we are connected to the wrong host");

        // ensure correct curl call
        if ($method == Zend_Http_Client::GET) {
            $curlMethod = CURLOPT_HTTPGET;
        } elseif ($method == Zend_Http_Client::POST) {
            $curlMethod = CURLOPT_POST;
        } else {
            // TODO: use CURLOPT_PUT for PUT requests, CURLOPT_CUSTOMREQUEST for other types of calls
            // For now, through an exception for unsupported request methods
            throw new Zend_Http_Client_Adapter_Exception("Method currently not supported");
        }

        // get http version to use
        $curlHttp = ($http_ver = 1.1) ? CURL_HTTP_VERSION_1_1 : CURL_HTTP_VERSION_1_0;

        curl_setopt($this->curl, $curlMethod, true);
        curl_setopt($this->curl, $curlHttp, true);

        // ensure headers are also returned
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        // ensure actual response is returned
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        // set additional headers
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

        if ($method == Zend_Http_Client::POST) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
        }

        // send the request
        $this->response = curl_exec($this->curl);

    }

    /**
	 * Return read response from server
	 *
	 * @return string
	 */
    public function read()
    {
        return $this->response;
    }

    /**
	 * Close the connection to the server
	 *
	 */
    public function close()
    {
        curl_close($this->curl);
        $this->curl = null;
        $this->connected_to = array(null, null);
    }

    /**
	 * Destructor: make sure curl is disconnected
	 *
	 */
    public function __destruct()
    {
        if ($this->curl) $this->close();
    }
}