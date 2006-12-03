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
require_once 'Zend/Http/Response.php';
require_once 'Zend/Http/Client/Adapter/Interface.php';

/**
 * A testing-purposes adapter. 
 * 
 * Should be used to test all components that rely on Zend_Http_Client, 
 * without actually performing an HTTP request. You should instantiate this 
 * object manually, and then set it as the client's adapter. Then, you can 
 * set the expected response using the setResponse() method. 
 *
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Client_Adapter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_Client_Adapter_Test implements Zend_Http_Client_Adapter_Interface 
{
    /**
     * Parameters array
     *
     * @var array
     */
    protected $config = array();
    
    /**
     * Response string to be returned by this adapter. Can be set using
     * setResponse().
     *
     * @var string
     */
    protected $response = "HTTP/1.1 400 Bad Request\r\n\r\n";
    
    /**
     * Adapter constructor, currently empty. Config is set using setConfig()
     *
     */
    public function __construct() 
    { }
    
    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array()) 
    {
        if (! is_array($config))
            throw Zend::exception('Zend_Http_Client_Adapter_Exception', 
                '$config expects an array, ' . gettype($config) . ' recieved.');
            
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
    { }
    
    /**
     * Send request to the remote server
     *
     * @param string $method
     * @param Zend_Uri_Http $uri
     * @param float $http_ver
     * @param array  $headers
     * @param string $body
     * @return string Request as string
     */
    public function write($method, $uri, $http_ver = 1.1, $headers = array(), $body = '')
    {
        $host = $uri->getHost();
            $host = (strtolower($uri->getScheme()) == 'https' ? 'sslv2://' . $host : $host);

        // Build request headers
        $path = $uri->getPath();
        if ($uri->getQuery()) $path .= '?' . $uri->getQuery();
        $request = "{$method} {$path} HTTP/{$http_ver}\r\n";
        foreach ($headers as $k => $v) {
            if (is_string($k)) $v = ucfirst($k) . ": $v";
            $request .= "$v\r\n";
        }
        
        // Add the request body
        $request .= "\r\n" . $body;
        
        // Do nothing - just return the request as string
        
        return $request;
    }
    
    /**
     * Return the response set in $this->setResponse()
     *
     * @return string
     */
    public function read()
    {
        return $this->response;
    }
    
    /**
     * Close the connection (dummy)
     *
     */
    public function close()
    { }
    
    /**
     * Set the HTTP response to be returned by this adapter
     *
     * @param Zend_Http_Response|string $response
     */
    public function setResponse($response)
    {
    	if ($response instanceof Zend_Http_Response) $response = $response->asString();
    	
    	$this->response = $response;
    }
}
