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
 * @package    Zend_Rest
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Zend_Rest_Client_Result
 */
require_once 'Zend/Rest/Client/Result.php';

/**
 * Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Rest_Client
{
    /**
     * Data for the query
     * @var array
     */
    protected $_data = array();

    /**
     * Zend_Http_Client
     * @var Zend_Http_Client
     */
    protected $_httpClient;

     /**
     * Zend_Uri of this web service
     * @var Zend_Uri_Http
     */
    protected $_uri = null;
    
    /**
     * Constructor
     * 
     * @param string|Zend_Uri_Http $uri URI for the web service
     * @return void
     */
    public function __construct($uri = null)
    {
        if (!empty($uri)) {
            $this->setUri($uri);
        }
    }

    /**
     * Set the URI to use in the request
     * 
     * @param string|Zend_Uri_Http $uri URI for the web service
     * @return Zend_Rest_Client
     */
    public function setUri($uri)
    {
    	if ($uri instanceof Zend_Uri_Http) {
    		$this->_uri = $uri;
    	} else {
    		$this->_uri = Zend_Uri::factory($uri);
    	}

        return $this;
    }

	/**
	 * Call a remote REST web service URI and return the Zend_Http_Response object
	 *
	 * @param  string $path            The path to append to the URI
	 * @throws Zend_Rest_Exception
	 * @return void
	 */
	final private function _prepareRest($path, $query = null)
	{
		// Get the URI object and configure it
		if (!$this->_uri instanceof Zend_Uri_Http) {
            require_once 'Zend/Rest/Client/Exception.php';
		    throw new Zend_Rest_Client_Exception('URI object must be set before performing call');
		}
		
		$uri = $this->_uri->getUri();
		
		if ($path[0] != '/' && $uri[strlen($uri)-1] != '/') {
			$path = '/' . $path;
		}

		$this->_uri->setPath($path);
		if (!is_null($query)) {
			$this->_uri->setQuery($query);
		}
		
		/**
		 * Get the HTTP client and configure it for the endpoint URI.  Do this each time
		 * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
		 */
		$this->getHttpClient()->setUri($this->_uri);
	}

	/**
	 * Performs an HTTP GET request to the $path.
	 *
	 * @param string $path
	 * @return Zend_Http_Response
	 */
	final public function restGet($path, $query = null)
	{
	   $this->_prepareRest($path, $query);
	   return $this->getHttpClient()->request('GET');
	}

	/**
	 * Performs an HTTP POST request to $path.
	 *
	 * @param string $path
	 * @param array $data
	 * @return Zend_Http_Response
	 */
	final public function restPost($path, $data)
	{
	   $this->_prepareRest($path);
	   return $this->getHttpClient()->request('POST');
	}

	/**
	 * Performs an HTTP PUT request to $path.
	 *
	 * @param string $path
	 * @param array $data
	 * @return Zend_Http_Response
	 */
	final public function restPut($path, $data)
	{
	   $this->_prepareRest($path);
	   return $this->getHttpClient()->request('PUT');;
	}

	/**
	 * Performs an HTTP DELETE request to $path.
	 *
	 * @param string $path
	 * @return Zend_Http_Response
	 */
	final public function restDelete($path)
	{
	   $this->_prepareRest($path);
	   return $this->getHttpClient()->request('DELETE');
	}
	
	/**
	 * Method call overload
	 *
	 * @param string $method Method name
	 * @param array $args Method args
	 * @return Zend_Rest_Client_Result|Zend_Rest_Client
	 */
	public function __call($method, $args)
	{
		$methods = array('post', 'get', 'delete', 'put');
		
		if (in_array(strtolower($method), $methods)) {
			if (!isset($args[0])) {
				$args[0] = $this->_uri->getPath();
			}
			$this->_data['rest'] = 1;
			$response = $this->{'rest' . $method}($args[0], $this->_data);
			$sxml = new Zend_Rest_Client_Result($response->getBody());
			return $sxml;
		} else {
			if (sizeof($args) == 1) {
				// More than one arg means it's definitely a Zend_WebService_Rest_Server
				$this->_data[$method] = $args[0];
				$this->_data['arg1'] = $args[0];
			} else {
				$this->_data['method'] = $method;
				if (sizeof($args) > 0) {
					foreach ($args as $key => $arg) {
						$key = 'arg' . $key;
						$this->_data[$key] = $arg;
					}
				}
			}
			return $this;
		}
	}

    /**
     * Retrieve HTTP client
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient instanceof Zend_Http_Client) {
            $this->_httpClient = new Zend_Http_Client();
        }

        return $this->_httpClient;
    }

    /**
     * Set HTTP Client
     *
     * @param Zend_Http_Client $value
     * @return Zend_Rest_Client
     */
    public function setHttpClient(Zend_Http_Client $client)
    {
        $this->_httpClient = $client;
        return $this;
    }
}
