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
require_once 'Zend/Http/Exception.php';
require_once 'Zend/Http/Client/Abstract.php';
require_once 'Zend/Http/Cookie.php';
require_once 'Zend/Http/Cookiejar.php';

/**
 * Zend_Http_Client is an implemetation of an HTTP client in PHP. The client 
 * supports basic features like sending different HTTP requests and handling
 * redirections, as well as more advanced features like proxy settings, HTTP
 * authentication and cookie persistance (using a Zend_Http_Cookiejar object)
 * 
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Client
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_Client extends Zend_Http_Client_Abstract 
{
    /**
     * Supported HTTP Authentication methods
     *
     */
    const AUTH_BASIC = 'basic';
    //const AUTH_DIGEST = 'digest'; <-- not implemented yet

    /**
     * Maximum number of redirections to follow, 0 for none.
     *
     * @var int
     */
    protected $maxRedirects = 5;
    
    /**
     * Redirection counter
     *
     * @var int
     */
    protected $redirectCounter = 0;
    
    /**
     * Whether to strictly follow RFC 2616 when redirecting
     * 
     * If true, 301 & 302 responses will be treated as written in the RFC - 
     * that is the same request method will be used in the new request. If 
     * false (default), a GET request is always used in the next request.
     *
     * @var boolean
     */
    protected $doStrictRedirects = false;
    
    /**
     * HTTP proxy settings
     *
     * @var array
     */
    protected $proxy = array('host' => null, 'port' => null, 'user' => null, 'password' => null);
    
    /**
     * HTTP Authentication settings
     *
     * Expected to be an associative array with this structure:
     * $this->auth = array('user' => 'username', 'password' => 'password', 'type' => 'basic')
     * Where 'type' should be one of the supported authentication types (see the AUTH_* 
     * constants), for example 'basic' or 'digest'.
     * 
     * If null, no authentication will be used.
     * 
     * @var array|null
     */
    protected $auth;

    /**
     * File upload arrays (used in POST requests)
     * 
     * An associative array, where each element is of the format:
     *   'name' => array('filename.txt', 'text/plain', 'This is the actual file contents')
     *
     * @var array
     */
    protected $files = array();
    
    /**
     * The client's cookie jar
     *
     * @var Zend_Http_Cookiejar
     */
    protected $Cookiejar = null;
    
    /**
     * Set the number of maximum redirections to follow, 0 for none.
     *
     * @param int $redirects
     * @return Zend_Http_Client
     */
    public function setMaxRedirects($redirects = 5)
    {
        $this->maxRedirects = (int) $redirects;
        return $this;
    }
    
    /**
     * Set whether to strictly follow RFC 2616 when redirecting or not
     * (See documentation for Zend_Http_Client::doStrictRedirects for details)
     *
     * @param boolean $strict
     * @return Zend_Http_Client
     */
    public function setStrictRedirects($strict = true)
    {
        $this->doStrictRedirects = $strict;
        return $this;
    }
    
    /**
     * Get the number of redirections done on the last request
     *
     * @return int
     */
    public function getLastRedirectionsCount()
    {
    	return $this->redirectCounter;
    }

    /**
     * Set a proxy server for the request
     *
     * @param string|null $host Hostname or null to disable proxy
     * @param int $port 
     * @param string $user
     * @param string $password
     * @return Zend_Http_Client
     */
    public function setProxy($host, $port = 8080, $user = null, $password = null)
    {
        $this->proxy = array(
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'password' => $password
        );
        
        return $this;
    }
    
    /**
     * Set HTTP authentication parameters
     * 
     * $type should be one of the supported types - see the self::AUTH_* 
     * constants.
     *  
     * To enable authentication: 
     *     @example $this->setAuth('shahar', 'secret', Zend_Http_Client::AUTH_BASIC);
     * To disable authentication: 
     *     @example $this->setAuth(false);
     *
     * @see http://www.faqs.org/rfcs/rfc2617.html
     * @param string|false $user User name or false disable authentication
     * @param string $password Password
     * @param string $type Authentication type
     * @return Zend_Http_Client
     */
    public function setAuth($user, $password = '', $type = self::AUTH_BASIC)
    {
        // If we got false or null, disable authentication
        if ($user === false || $user === null) {
            $this->auth = null;
            
        // Else, set up authentication
        } else {
            // Check we got a proper authentication type
            if (! defined('self::AUTH_' . strtoupper($type)))
                throw new Zend_Http_Exception("Invalid or not supported authentication type: '$auth'");

            $this->auth = array(
                'user' => (string) $user,
                'password' => (string) $password,
                'type' => $type
            );
        }
        
        return $this;
    }

    /**
     * Set the HTTP client's cookie jar.
     * 
     * A cookie jar is an object that holds and maintains cookies across HTTP requests
     * and responses.
     *
     * @param Zend_Http_Cookiejar|boolean $cookiejar Exisitng cookiejar object, true to create a new one, false to disable
     * @return Zend_Http_Client
     */
    public function setCookiejar($cookiejar = true)
    {
        if ($cookiejar instanceof Zend_Http_Cookiejar) {
            $this->Cookiejar = $cookiejar;
        } elseif ($cookiejar === true) {
            $this->Cookiejar = new Zend_Http_Cookiejar();
        } elseif (! $cookiejar) {
            $this->Cookiejar = null;
        } else {
            throw new Zend_Http_Exception('Invalid parameter type passed as Cookiejar');
        }
        
        return $this;
    }
    
    /**
     * Return the current cookie jar or null if none.
     *
     * @return Zend_Http_Cookiejar|null
     */
    public function getCookiejar()
    {
        return $this->Cookiejar;
    }
    
    /**
     * Add a cookie to the request. If the client has no Cookie Jar, the cookies 
     * will be added directly to the headers array as "Cookie" headers.
     *
     * @param Zend_Http_Cookie|string $cookie
     * @param string|null $value If "cookie" is a string, this is the cookie value. 
     * @return Zend_Http_Client
     */
    public function setCookie($cookie, $value = null)
    {
        if (! is_null($value)) $value = urlencode($value);
        
        if (isset($this->Cookiejar)) {
            if ($cookie instanceof Zend_Http_Cookie) {
                $this->Cookiejar->addCookie($cookie);
            } elseif (is_string($cookie) && ! is_null($value)) {
                if (preg_match("/[=,; \t\r\n\013\014]/", $cookie))
                    throw new Zend_Http_Exception(
                        "Cookie name cannot contain these characters: =,; \t\r\n\013\014 ({$name})");
                
                $cookie = Zend_Http_Cookie::factory("{$cookie}={$value}", $this->uri);
                $this->Cookiejar->addCookie($cookie);
            }
        } else {
            parent::setCookie($cookie, $value);
        }
        
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
        parent::resetParameters();
        $this->files = array();
        
        return $this;
    }
    
    /**
     * Set a file to upload (using a POST request)
     *
     * Can be used in two ways:
     *
     * 1. $data is null (default): $filename is treated as the name if a local file which
     *    will be read and sent. Will try to guess the content type using mime_content_type().
     * 2. $data is set - $filename is sent as the file name, but $data is sent as the file
     *    contents and no file is read from the file system. In this case, you need to 
     *    manually set the content-type ($ctype) or it will default to 
     *    application/octet-stream.
     * 
     * @param string $filename Name of file to upload, or name to save as
     * @param string $formname Name of form element to send as
     * @param string $data Data to send (if null, $filename is read and sent)
     * @param string $ctype Content type to use (if $data is set and $ctype is 
     *     null, will be application/octet-stream)
     * @return Zend_Http_Client
     */
    public function setFileUpload($filename, $formname, $data = null, $ctype = null)
    {
        if (is_null($data)) {
            if (! $data = @file_get_contents($filename))
                throw new Zend_Http_Exception("Unable to read file '{$filename}' for upload");

            if (! $ctype && function_exists('mime_content_type')) $ctype = mime_content_type($filename);
        }
        
        // Force enctype to multipart/form-data
        $this->setEncType(self::ENC_FORMDATA);

        if (is_null($ctype)) $ctype = 'application/octet-stream';
        $this->files[$formname] = array($filename, $ctype, $data);
        
        return $this;
    }
    
    /**
     * Send the HTTP request and return an HTTP response object
     *
     * @param string $method
     * @return Zend_Http_Response
     */
    public function request($method = null) 
    {
        $this->redirectCounter = 0;
        $response = null;
        
        // Send the first request. If redirected, continue.
        do {
            $response = parent::request($method);

            // Load cookies into cookie jar
            if (isset($this->Cookiejar)) $this->Cookiejar->addCookiesFromResponse($response, $this->uri);

            // If we got redirected, look for the Location header
            if ($response->isRedirect() && ($location = $response->getHeader('location'))) {
            	
                // Check whether we send the exact same request again, or drop the parameters
                // and send a GET request
                if ($response->getStatus() == 303 ||
                ((! $this->doStrictRedirects) && ($response->getStatus() == 302 || $response->getStatus() == 301))) {
                    $this->resetParameters();
                    $this->setMethod(self::METHOD_GET);
                }

                // If we got a well formed absolute URI
                if (Zend_Uri_Http::check($location)) {
                    $this->setHeaders('host', null);
                    $this->setUri($location);

                } else {

                    // Split into path and query and set the query
                    list($location, $query) = explode('?', $location, 2);
                    $this->uri->setQueryString($query);

                    // Else, if we got just an absolute path, set it
                    if(strpos($location, '/') === 0) {
                        $this->uri->setPath($location);

                        // Else, assume we have a relative path
                    } else {
                        // Get the current path directory, removing any trailing slashes
                        $path = rtrim(dirname($this->uri->getPath()), "/");
                        $this->uri->setPath($path . '/' . $location);
                    }
                }
                $this->redirectCounter++;
                
            } else {
            	// If we didn't get any location, stop redirecting
                break;
            }
            
        } while ($this->redirectCounter < $this->maxRedirects);

        return $response;
    }
    
    /**
     * Prepare the request headers
     * 
     * @return string
     */
    protected function _prepare_headers()
    {
        $headers = "{$this->method} {$this->uri->getPath()}";
        
        // Get the original GET parameters from the URL, merge them to manually
        // set GET parameters and set the query string
        $uri_params = array();
        parse_str($this->uri->getQuery(), $uri_params);
        $query = http_build_query(array_merge($uri_params, $this->paramsGet));
        
        if ($query) $headers .= "?{$query}";
        $headers .= " HTTP/{$this->http_version}\r\n";
        
        // Set the host header
        if (! isset($this->headers['host'])) {
            $host = $this->uri->getHost() . ($this->uri->getPort() == 80 ? '' : ':' . $this->uri->getPort());
            $headers .= "Host: {$host}\r\n";
        }
        
        // Set the connection header
        // For now, only support closed connections
        if (! isset($this->headers['connection'])) {
            $headers .= "Connection: close\r\n";
        }
        
        // Set the content-type header
        if (! isset($this->headers['content-type']) && isset($this->enctype)) {
			$headers .= "Content-type: {$this->enctype}\r\n";
        }
        
        // Set the user agent header
        if (! isset($this->headers['user-agent']) && isset($this->user_agent)) {
			$headers .= "User-agent: {$this->user_agent}\r\n";
        }
        
        // Set HTTP authentication if needed
        if (is_array($this->auth)) {
        	$auth = self::encodeAuthHeader($this->auth['user'], $this->auth['password'], $this->auth['type']);
            $headers .= "Authorization: {$auth}\r\n"; 
        }

        // Load cookies from cookie jar
        if (isset($this->Cookiejar)) {
            $cookstr = $this->Cookiejar->getMatchingCookies($this->uri, 
                true, Zend_Http_Cookiejar::COOKIE_STRING_CONCAT);
                
            if ($cookstr) $headers .= "Cookie: {$cookstr}\r\n";
        }
        
        // Add all other user defined headers
        foreach ($this->headers as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $subval) {
                    $headers .= ucfirst($name) . ": {$subval}\r\n";
                }
            } else {
                $headers .= ucfirst($name) . ": {$value}\r\n";
            }
        }
        
        return $headers;
    }
    
    /**
     * Prepare the request body (for POST and PUT requests)
     * 
     * @return string
     */
    protected function _prepare_body()
    {
        // According to RFC2616, a TRACE request should not have a body.
        if ($this->method == self::METHOD_TRACE) {
            return '';
        }
        
        // If we have raw_post_data set, just use it as the body.
        if (isset($this->raw_post_data)) {
            $this->setHeaders('content-length', strlen($this->raw_post_data));
            return $this->raw_post_data;
        }
        
        $body = '';
        
        // If we have files to upload, force enctype to multipart/form-data
        if (count ($this->files) > 0) $this->setEncType(self::ENC_FORMDATA);

        // If we have POST parameters or files, encode and add them to the body
        if (count($this->paramsPost) > 0 || count($this->files) > 0) {
            switch($this->enctype) {
                case self::ENC_FORMDATA:
                    // Encode body as multipart/form-data
                    $boundary = '---ZENDHTTPCLIENT-' . md5(microtime());
                    $this->setHeaders('Content-type', self::ENC_FORMDATA . "; boundary={$boundary}");
                    
                    // Get POST parameters and encode them
                    $params = $this->_getParametersRecursive($this->paramsPost);
                    foreach ($params as $pp) {
                        $body .= self::encodeFormData($boundary, $pp[0], $pp[1]);
                    }
                    
                    // Encode files
                    foreach ($this->files as $name => $file) {
                        $fhead = array('Content-type' => $file[1]);
                        $body .= self::encodeFormData($boundary, $name, $file[2], $file[0], $fhead);
                    }
                    
                    $body .= "--{$boundary}--\r\n";
                    break;
                
                case self::ENC_URLENCODED:
                    // Encode body as application/x-www-form-urlencoded
                    $this->setHeaders('Content-type', self::ENC_URLENCODED);
                    $body = http_build_query($this->paramsPost);
                    break;
                
                default:
                    throw new Zend_Http_Exception("Cannot handle content type '{$this->enctype} automaically." . 
                        " Please use Zend_Http_Client::setRawData to send this kind of content.");
                    break;
            }
        }
        
        if ($body) $this->setHeaders('content-length', strlen($body));
        return $body;
    }
    
    /**
     * Open a connection to the remote server
     * 
     * @return resource Socket
     */
    protected function _connect()
    {
        // If the URI should be accessed via SSL, prepend the Hostname with ssl://
        $host = ($this->uri->getScheme() == 'https') ? 'ssl://' . $this->uri->getHost() : $this->uri->getHost();
        $socket = @fsockopen($host, $this->uri->getPort(), $errno, $errstr, $this->timeout);
        if (! $socket) {
            // Added more to the exception message, $errstr is not always populated and the message means nothing then.
            throw new Zend_Http_Exception('Unable to Connect to ' . $this->uri->getHost() . ': ' . $errstr .
                ' (Error Number: ' . $errno . ')');
        }
        
        return $socket;
    }
        
    /**
     * Send request to the remote server
     *
     * @param resource $socket Socket (returned by _connect())
     * @param string $request Request to send
     */
    protected function _write($socket, $request)
    {
        fwrite($socket, $request);
    }
    
    /**
     * Read response from remote server
     *
     * @param resource $socket Socket (returned by _connect())
     * @return string
     */
    protected function _read($socket)
    {
        $response = '';
        while ($buff = fread($socket, 8192)) {
            $response .= $buff;
        }
        
        fclose($socket);
        
        return $response;
    }
    
    /**
     * Helper method that gets a possibly multi-level parameters array (get or
     * post) and flattens it.
     * 
     * The method returns an array of (key, value) pairs (because keys are not
     * necessarily unique. If one of the parameters in as array, it will also 
     * add a [] suffix to the key.
     *
     * @param array $parray The parameters array
     * @param bool $urlencode Whether to urlencode the name and value
     * @return array
     */
    protected function _getParametersRecursive($parray, $urlencode = false) 
    {
        if (! is_array($parray)) return $parray;
        $parameters = array();
        
        foreach ($parray as $name => $value) {
            if ($urlencode) $name = urlencode($name);
            
            // If $value is an array, iterate over it
            if (is_array($value)) {
            	$name .= ($urlencode ? '%5B%5D' : '[]');
                foreach ($value as $subval) {
                    if ($urlencode) $subval = urlencode($subval);
                    $parameters[] = array($name, $subval);
                }
            } else {
                if ($urlencode) $value = urlencode($value);
                $parameters[] = array($name, $value);
            }
        }
        
        return $parameters;
    }
    
    /**
     * Encode data to a multipart/form-data part suitable for a POST request.
     *
     * @param string $boundary
     * @param string $name
     * @param mixed $value
     * @param string $filename
     * @param array $headers Associative array of optional headers @example ("Content-transfer-encoding" => "binary")
     * @return string
     */
    static public function encodeFormData($boundary, $name, $value, $filename = null, $headers = array()) {
        $ret = "--{$boundary}\r\n" .
            "Content-Disposition: form-data; name={$name}";
            
        if ($filename) $ret .= "; filename={$filename}";
        $ret .= "\r\n";
        
        foreach ($headers as $hname => $hvalue) {
            $ret .= "{$hname}: {$hvalue}\r\n";
        }
        $ret .= "\r\n";
        
        $ret .= "{$value}\r\n";
        
        return $ret;
    }

    /**
     * Create a HTTP authentication "Authorization:" header according to the 
     * specified user, password and authentication method.
     *
     * @see http://www.faqs.org/rfcs/rfc2617.html
     * @param string $user
     * @param string $password
     * @param string $type
     * @return string
     */
    static public function encodeAuthHeader($user, $password, $type = self::AUTH_BASIC)
    {
        $authHeader = null;
        
        switch ($type) {
            case self::AUTH_BASIC:
                // In basic authentication, the user name cannot contain ":"
                if (strpos($user, ':') !== false)
                    throw new Exception("The user name cannot contain ':' in 'Basic' HTTP authentication");

                $authHeader = 'Basic ' . base64_encode($user . ':' . $password);
                break;
                
            //case self::AUTH_DIGEST:
                /**
                 * @todo Implement digest authentication
                 */
            //    break;
                
            default:
                throw new Zend_Http_Exception("Not a supported HTTP authentication type: '$type'");
        }
        
        return $authHeader;
    }
}
