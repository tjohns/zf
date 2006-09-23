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
 * @subpackage Client
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend.php';
require_once 'Zend/Uri.php';
require_once 'Zend/Http/Response.php';
require_once 'Zend/Http/Exception.php';

/**
 * Zend_Http_Client_Abstract is the abstract of a Zend HTTP Client class. For
 * the default implementation, use the Zend_Http_Client class.
 * 
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Client
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Http_Client_Abstract
{
    /**
     * HTTP protocol versions
     */
    const HTTP_VER_1 = 1.1;
    const HTTP_VER_0 = 1.0;
    
    /**
     * HTTP request methods 
     */
    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE   = 'TRACE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_CONNECT = 'CONNECT';
    
    /**
     * POST data encoding methods
     */
    const ENC_URLENCODED = 'application/x-www-form-urlencoded';
    const ENC_FORMDATA   = 'multipart/form-data';
    
    /**
     * The user agent string that identifies the HTTP client 
     *
     * @var string
     */
    protected $user_agent = null;
    
    /**
     * Request URI
     *
     * @var Zend_Uri_Http
     */
    protected $uri;
    
    /**
     * Request timeout in seconds
     *
     * @var int
     */
    protected $timeout = 10;
    
    /**
     * Associative array of request headers 
     *
     * @var array
     */
    protected $headers = array();
    
    /**
     * Request HTTP version (1.0 or 1.1)
     *
     * @var float
     */
    protected $http_version = self::HTTP_VER_1;
    
    /**
     * HTTP request method
     *
     * @var string
     */
    protected $method = self::METHOD_GET;
    
    /**
     * Associative array of GET parameters
     *
     * @var array
     */
    protected $paramsGet = array();
    
    /**
     * Assiciative array of POST parameters
     *
     * @var array
     */
    protected $paramsPost = array();
    
    /**
     * Request body content type (for POST requests)
     *
     * @var string
     */
    protected $enctype = null;
    
    /**
     * The raw post data to send. Could be set by setRawPostData($data, $enctype).
     *
     * @var string
     */
    protected $raw_post_data = null;
    
    /**
     * The last HTTP request sent by the client, as string
     *
     * @var string
     */
    protected $last_request = null;
    
    /**
     * Contructor method. Will create a new HTTP client. Accepts the target
     * URL and optionally and array of headers.
     *
     * @param Zend_Uri_Http|string $uri
     * @param array $headers Optional request headers to set
     */
    public function __construct($uri = null, $headers = null)
    {
        if (! is_null($uri)) $this->setUri($uri);
        if (! is_null($headers)) $this->setHeaders($headers);
        $this->user_agent = 'PHP/' . PHP_VERSION . ' Zend Framework/0.1.3';
    }
    
    /**
     * Set the URI for the next request
     *
     * @param Zend_Uri_Http|string $uri
     * @return Zend_Http_Client
     */
    public function setUri($uri)
    {
        if (is_string($uri) && Zend_Uri_Http::check($uri)) {
            $uri = Zend_Uri_Http::factory($uri);
        }
        
        if ($uri instanceof Zend_Uri_Http) {
            // We have no ports, set the defaults
            if (! $uri->getPort()) {
                $uri->setPort(($uri->getScheme() == 'https' ? 443 : 80));
            }
            
            $this->uri = $uri;
        } else {
            throw new Zend_Http_Exception('Passed parameter is not a valid HTTP URI.');
        }
        
        return $this;
    }
    
    /**
     * Get the URI for the next request
     *
     * @param boolean $as_string If true, will return the URI as a string
     * @return Zend_Uri_Http|string
     */
    public function getUri($as_string = false)
    {
        if ($as_string && $this->uri instanceof Zend_Uri_Http) {
            return $this->uri->__toString();
        } else {
            return $this->uri;
        }
    }
    
    /**
     * Set the user agent identification string
     *
     * @param string $ua
     * @return Zend_Http_Client
     */
    public function setUserAgent($ua) {
        $this->user_agent = $ua;
        return $this;
    }
    
    /**
     * Set the client's connection timeout in seconds, 0 for none
     *
     * @param int $timeout
     * @return Zend_Http_Client
     */
    public function setTimeout($timeout = 10)
    {
        $this->timeout = $timeout;
        return $this;
    }
    
    /**
     * Set the next request's method
     * 
     * Validated the passed method and sets it. If we have files set for 
     * POST requests, and the new method is not POST, the files are silently
     * dropped. 
     *
     * @param string $method
     * @return Zend_Http_Client
     */
    public function setMethod($method = self::METHOD_GET)
    {
        $method = strtoupper($method);
        
        if (! defined('self::METHOD_' . $method)) {
            throw new Zend_Http_Exception("'{$method}' is not a valid HTTP request method.");
        }
        
        if ($method == self::METHOD_POST && is_null($this->enctype)) {
            $this->setEncType(self::ENC_URLENCODED);
        }
        
        $this->method = $method;
        
        return $this;
    }
    
    /**
     * Get the currently-set request method (GET, POST, etc.)
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Set one or more request headers
     * 
     * This function can be used in several ways to set the client's request
     * headers:
     * 1. By providing two parameters: $name as the header to set (eg. 'Host')
     *    and $value as it's value (eg. 'www.example.com').
     * 2. By providing a single header string as the only parameter
     *    eg. 'Host: www.example.com'
     * 3. By providing an array of headers as the first parameter
     *    eg. array('host' => 'www.example.com', 'x-foo: bar'). In This case 
     *    the function will call itself recursively for each array item.
     * 
     * In all cases, the third parameter $override decides whether to overwrite
     * the value of this header if it's already set (default), or to add another
     * header with the same name to the list of headers (good for repeating
     * headers like 'Cookie').
     * 
     * @param string|array $name Header name, full header string ('Header: value')
     *     or an array of headers
     * @param mixed $value Header value or null
     * @param boolean $override Whether to override header if it already exists
     * @return Zend_Http_Client
     */
    public function setHeaders($name, $value = null, $override = true)
    {
	    // If we got an array, go recusive!
    	if (is_array($name)) {
    		foreach ($name as $k => $v) {
    			if (is_string($k)) {
                	$this->setHeaders($k, $v, $override);
            	} else {
                	$this->setHeaders($v, null, $override);
            	}
    		}
    	} else {
            // Check if $name needs to be split
            if (is_null($value) && (strpos($name, ':') > 0)) 
                list($name, $value) = explode(':', $name, 2);
            
            // Make sure name is valid
            if (! preg_match('/^[A-Za-z0-9-]+$/', $name)) {
                throw new Zend_Http_Exception("{$name} is not a valid HTTP header name");
            }
        
            // Header names are storred lowercase internally.
            $name = strtolower($name);
        
            // If $value is null or false, unset the header
            if ($value === null || $value === false) {
        	    unset($this->headers[$name]);
        	
            // Else, set the header
            } else {
        	    if (is_string($value)) $value = trim($value);
        	
                // If override is set, set the header as is
                if ($override || ! isset($this->headers[$name])) {
                    $this->headers[$name] = $value;
            
                // Else, if the header already exists, add a new value
                } else {
                    if (! is_array($this->headers[$name])) 
                        $this->headers[$name] = array($this->headers[$name]);

                    $this->headers[$name][] = $value;
                }
            }
    	}
    	
        return $this;
    }
    
    /**
     * Get the value of a specific header
     *
     * Note that if the header has more than one value, an array
     * will be returned. 
     * 
     * @param unknown_type $key
     * @return string|array|null The header value or null if it is not set
     */
    public function getHeader($key)
    {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        } else {
            return null;
        }
    }
    
    /**
     * Set a GET parameter for the request. Wrapper around _setParameter
     *
     * @param string $name
     * @param string $value
     * @param boolean $override Whether to overwrite the parameter's value
     * @return Zend_Http_Client
     */    
    public function setParameterGet($name, $value, $override = true)
    {
        $this->_setParameter('GET', $name, $value, $override);
        return $this;
    }
    
    /**
     * Set a POST parameter for the request. Wrapper around _setParameter
     *
     * @param string $name
     * @param string $value
     * @param boolean $override Whether to overwrite the parameter's value
     * @return Zend_Http_Client
     */        
    public function setParameterPost($name, $value, $override = true)
    {
        $this->_setParameter('POST', $name, $value, $override);
        return $this;
    }
    
    /**
     * Clear all GET and POST parameters
     * 
     * Should be used to reset the request parameters if the client is 
     * used for several concurrent requests.
     * 
     * @return Zend_Http_Client
     */
    public function resetParameters()
    {
        // Reset parameter data
        $this->paramsGet = array();
        $this->paramsPost = array();
        $this->raw_post_data = null;
        
        // Clear outdated headers
        if (isset($this->headers['content-type'])) unset($this->headers['content-type']);
        if (isset($this->headers['content-length'])) unset($this->headers['content-length']);
        
        return $this;
    }
    
    /**
     * Set a cookie parameter
     *
     * @param Zend_Http_Cookie|string $name
     * @param string|null $value If "cookie" is a string, this is the cookie value. 
     * 
     * @return Zend_Http_Client
     */
    public function setCookie($cookie, $value = null)
    {
        if ($cookie instanceof Zend_Http_Cookie) {
            $cookie = $cookie->getName();
            $value = $cookie->getValue();
        }
        
        if (preg_match("/[=,; \t\r\n\013\014]/", $cookie))
            throw new Zend_Http_Exception("Cookie name cannot contain these characters: =,; \t\r\n\013\014 ({$name})");
            
        $value = urlencode($value);
        
        if (isset($this->headers['cookie'])) {
            $this->headers['cookie'] .= "; {$cookie}={$value}";
        } else {
            $this->setHeaders('cookie', "{$cookie}={$value}");
        }
        
        return $this;
    }
        
    /**
     * Set the encoding type for POST data
     *
     * @param string $enctype
     * @return Zend_Http_Client
     */
    public function setEncType($enctype = self::ENC_URLENCODED)
    {
        $this->enctype = $enctype;
        
        return $this;
    }
    
    /**
     * Set the raw (already encoded) POST data. 
     *
     * This function is here for two reasons: 
     * 1. For advanced user who would like to set their own data, already encoded
     * 2. For backwards compatibilty: If someone uses the old post($data) method.
     *    this method will be used to set the encoded data. 
     *
     * @param string $data
     * @param string $enctype
     * @return Zend_Http_Client
     */
    public function setRawData($data, $enctype = null)
    {
        $this->raw_post_data = $data;
        $this->setEncType($enctype);
        
        return $this;
    }
    
    /**
     * Get the last HTTP request as string
     * 
     * @return string
     */
    public function getLastRequest() {
        return $this->last_request;
    }

    /**
     * Send the HTTP request and return a response
     *
     * @param string $method
     * @return Zend_Http_Response
     */
    public function request($method = null)
    {
        if (! $this->uri instanceof Zend_Uri_Http)
            throw new Zend_Http_Exception("No valid URI has been passed to the client");
            
        if ($method) $this->setMethod($method);
        
        // Prepare the request string
        $body = $this->_prepare_body();
        $headers = $this->_prepare_headers();
        $request = $headers . "\r\n" . $body;
        
        // Open the connection, send the request and read the response
        $sock = $this->_connect();
        $this->_write($sock, $request);
        $response = $this->_read($sock);
        
        $this->last_request = $request;
        
        return Zend_Http_Response::factory($response);
    }
    
    /**
     * Validate an array of headers.
     * 
     * Accepts either an associative array of Header name => Header value
     * format, or a numbered array where all elements are string of the
     * format "Header: value".
     *
     * @param array $headers
     * @return bool
     * @throws Zend_Http_Client_Exception
     */
    static public function validateHeaders($headers = array())
    {
        // Make sure we got the proper data
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                // If this is not an associative array, split the string
                if (! is_string($name))
                    list($name, $value) = explode(":", $value, 1);

                $value = trim($value);
                
                // Make sure the header is valid
                if (! preg_match("/^[a-zA-Z-]+$/", $name))
                    return false;
            }
        }
        else 
            throw new Zend_Http_Exception("Parameter must be an array of header lines.");
            
        return true;
    }
    
    /**
     * Set a GET or POST parameter - used by SetParameterGet and SetParameterPost
     *
     * @param string $type GET or POST
     * @param string $name
     * @param string $value
     * @param boolean $override Whether to replace old value, or add it as an array of values
     */
    protected function _setParameter($type, $name, $value, $override = true)
    {
        $type = strtolower($type);
        switch ($type) {
            case 'get':
                $parray = &$this->paramsGet;
                break;
            case 'post':
                $parray = &$this->paramsPost;
                break;
            default:
                throw new Zend_Http_Exception("Trying to set unknown parameter type: '{$type}'");
        }
        
        if ($override || (! isset($parray[$name]))) {
            $parray[$name] = $value;
        } elseif (isset($parray[$name])) {
            if (! is_array($parray[$name])) {
                $parray[$name] = array($parray[$name]);
            }
            $parray[$name][] = $value;
        }
    }

    /**
     * Prepare the request headers
     * 
     * @abstract 
     * @return string
     */
    abstract protected function _prepare_headers();
    
    /**
     * Prepare the request body (for POST and PUT requests)
     * 
     * @abstract 
     * @return string
     */
    abstract protected function _prepare_body();
    
    /**
     * Open a connection to the remote server
     * 
     * @abstract 
     * @return resource Socket
     */
    abstract protected function _connect();
    
    /**
     * Send request to the remote server
     *
     * @abstract 
     * @param resource $socket Socket (returned by _connect())
     * @param string $request Request to send
     */
    abstract protected function _write($socket, $request);
    
    /**
     * Read response from remote server
     *
     * @abstract 
     * @param resource $socket Socket (returned by _connect())
     * @return string
     */
    abstract protected function _read($socket);
        
    // ------------------------------------------------------------------------
    // Deprecated methods
    // ------------------------------------------------------------------------
    
    /**
     * Send a GET request
     *
     * @return Zend_Http_Response
     * @deprecated Please use request('GET') instead
     */
    public function get()
    {
        $this->setMethod(self::METHOD_GET);
        return $this->request();
    }
    
    /**
     * Send a POST request
     *
     * @param string $data Data to send 
     * @return Zend_Http_Response
     * @deprecated Please use request('POST') instead
     */
    public function post($data = null)
    {
        $this->setMethod(self::METHOD_POST);
        $this->setRawData($data);
        return $this->request();
    }
    
    /**
     * Send a PUT request
     *
     * @param string $data Data to send
     * @return Zend_Http_Response
     * @deprecated Please use request('PUT') instead
     */
    public function put($data = null)
    {
        $this->setMethod(self::METHOD_PUT);
        $this->setRawData($data);
        return $this->request();
    }
    
    /**
     * Send a DELETE request
     * 
     * @return Zend_Http_Response
     * @deprecated Please use request('DELETE') instead
     */
    public function delete()
    {
        $this->setMethod(self::METHOD_DELETE);
        return $this->request();
    }
}
