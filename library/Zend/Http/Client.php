<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Http
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Http_Client_Abstract
 */
require_once 'Zend/Http/Client/Abstract.php';


/**
 * @package    Zend_Http
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Http_Client extends Zend_Http_Client_Abstract
{
    /**
     * Class Constructor, create and validate Zend_Uri object
     *
     * @param  string|Zend_Uri|null $uri
     * @param  array $headers
     * @return void
     */
    public function __construct($uri = null, $headers = array())
    {
    	if ($uri !== null) {
    		$this->setUri($uri);
    	}

    	if ($headers !== array()) {
    		$this->setHeaders($headers);
    	}
    }


   /**
     * Send a GET HTTP Request
     *
     * @param  int $redirectMax Maximum number of HTTP redirections followed
     * @return Zend_Http_Response
     */
    public function get($redirectMax = 5)
    {
        /**
         * @todo Implement ability to send Query Strings
         */

        // Follow HTTP redirections, up to $redirectMax of them
        for ($redirect = 0; $redirect <= $redirectMax; $redirect++) {

            // Build the HTTP request
            $request = array_merge(array('GET ' . $this->_uri->getPath() . '?' . $this->_uri->getQuery() . ' HTTP/1.0',
                                         'Host: ' . $this->_uri->getHost() . ':' . $this->_uri->getPort(),
                                         'Connection: close'),
                                   $this->_headers);

            // Open a TCP connection
            $socket = $this->_openConnection();

            // Make the HTTP request
            fwrite($socket, implode("\r\n", $request) . "\r\n\r\n");

            // Fetch the HTTP response
            $response = $this->_read($socket);

            // If the HTTP response was a redirect, and we are allowed to follow additional redirects
            if ($response->isRedirect() && $redirect < $redirectMax) {

                // Fetch the HTTP response headers
                $headers = $response->getHeaders();

                // Attempt to find the Location header
                foreach ($headers as $headerName => $headerValue) {
                    // If we have a Location header
                    if (strtolower($headerName) == "location") {
                        // Set the URI to the new value
                        $this->setUri($headerValue);
                        // Continue with the new redirected request
                        continue 2;
                    }
                }
            }

            // No more looping for HTTP redirects
            break;
        }

        // Return the HTTP response
        return $response;
    }


    /**
     * Send a POST HTTP Request
     *
     * @param string $data Data to send in the request
     * @return Zend_Http_Response
     */
    public function post($data)
    {
        $socket = $this->_openConnection();

        $request = array_merge(array('POST ' . $this->_uri->getPath() . ' HTTP/1.0',
                                     'Host: ' . $this->_uri->getHost() . ':' . $this->_uri->getPort(),
                                     'Connection: close',
                                     'Content-length: ' . strlen($data)),
                               $this->_headers);

        fwrite($socket, implode("\r\n", $request) . "\r\n\r\n" . $data . "\r\n");

        return $this->_read($socket);
    }


    /**
     * Send a PUT HTTP Request
     *
     * @param string $data Data to send in the request
     * @return Zend_Http_Response
     */
    public function put($data)
    {
        $socket = $this->_openConnection();

        $request = array_merge(array('PUT ' . $this->_uri->getPath() . ' HTTP/1.0',
                                     'Host: ' . $this->_uri->getHost() . ':' . $this->_uri->getPort(),
                                     'Connection: close',
                                     'Content-length: ' . strlen($data)),
                               $this->_headers);

        fwrite($socket, implode("\r\n", $request) . "\r\n\r\n" . $data . "\r\n");

        return $this->_read($socket);
    }


    /**
     * Send a DELETE HTTP Request
     *
     * @return Zend_Http_Response
     */
    public function delete()
    {
        $socket = $this->_openConnection();

        $request = array_merge(array('DELETE ' . $this->_uri->getPath() . ' HTTP/1.0',
                                     'Host: ' . $this->_uri->getHost() . ':' . $this->_uri->getPort(),
                                     'Connection: close'),
                               $this->_headers);

        fwrite($socket, implode("\r\n", $request) . "\r\n\r\n");

        return $this->_read($socket);
    }


    /**
     * Open a TCP connection for our HTTP/SSL request.
     *
     * @throws Zend_Http_Client_Exception
     * @return resource Socket Resource
     */
    protected function _openConnection()
    {
    	if (!$this->_uri instanceof Zend_Uri) {
    		throw new Zend_Http_Client_Exception('URI must be set before performing remote operations');
    	}

        // If the URI should be accessed via SSL, prepend the Hostname with ssl://
        $host = ($this->_uri->getScheme() == 'https') ? 'ssl://' . $this->_uri->getHost() : $this->_uri->getHost();
        $socket = @fsockopen($host, $this->_uri->getPort(), $errno, $errstr, $this->_timeout);
        if (!$socket) {
            // Added more to the exception message, $errstr is not always populated and the message means nothing then.
            throw new Zend_Http_Client_Exception('Unable to Connect to ' . $this->_uri->getHost() . ': ' . $errstr .
                                                ' (Error Number: ' . $errno . ')');
        }
        return $socket;
    }


    /**
     * Read Data from the Socket
     *
     * @param Resource $socket Socket returned by {@see Zend_Http_Client::_openConnection()}
     * @return Zend_Http_Response
     */
    protected function _read($socket)
    {
    	$responseCode    = null;
    	$responseHeaders = array();
    	$responseBody    = null;

		$hdr = null;
        while (strlen($header = rtrim(fgets($socket, 8192)))) {
            if (preg_match('|HTTP/\d.\d (\d+) (\w+)|', $header, $matches)) {
                $responseCode = (int) $matches[1];
            } else if (preg_match('|^\s|', $header)) {
                if ($hdr !== null) {
	                $responseHeaders[$hdr] .= ' ' . trim($header);
                }
            } else {
                $pieces = explode(': ', $header, 2);
                $responseHeaders[$pieces[0]] = isset($pieces[1]) ? $pieces[1] : null;
            }
        }

        while (!feof($socket)) {
            $responseBody .= fgets($socket, 8192);
        }

        fclose($socket);

        return new Zend_Http_Response($responseCode, $responseHeaders, $responseBody);
    }
}


