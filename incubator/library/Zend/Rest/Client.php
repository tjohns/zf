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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Service_Abstract
 */
require_once 'Zend/WebService/Client/Abstract.php';

/**
 * Zend_Rest_Client_Result
 */
require_once 'Zend/Rest/Client/Result.php';

/**
 * Zend_Rest_Client_Exception
 */
require_once 'Zend/Rest/Client/Exception.php';

/**
 * Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage Client
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Rest_Client extends Zend_WebService_Client_Abstract
{
    /**
     * Zend_Uri of this web service
     *
     * @var Zend_Uri_Http
     */
    protected $_uri = null;
    
    /**
     * Data for the query
     * 
     * @var array
     */
    protected $_data = array();
    
    /**
     * Constructor
     * 
     * @param $uri Zend_Uri_Http|string URI for the web service
     */
    public function __construct($uri)
    {
    	if ($uri instanceof Zend_Uri_Http) {
    		$this->_uri = $uri;
    	} else {
    		$this->_uri = Zend_Uri::factory($uri);
    	}
    }

	/**
	 * Call a remote REST web service URI and return the Zend_Http_Response object
	 *
	 * @param  string $path            The path to append to the URI
	 * @throws Zend_Service_Exception
	 * @return void
	 */
	final private function _prepareRest($path, $query = null)
	{
		// Get the URI object and configure it
		if (!$this->_uri instanceof Zend_Uri_Http) {
		    throw new Zend_Service_Exception('URI object must be set before performing call');
		}
		
		$uri = $this->_uri->getUri();
		
		if ($path[0] != '/' && $uri[strlen($uri)-1] != '/') {
			$path = '/' . $path;
		}

		$this->_uri->setPath($path);

		if (!is_null($query) && is_string($query)) {
			$this->_uri->setQueryString($query);
		} elseif (is_array($query)) {
			$this->_uri->setQueryArray($query);
		}

		/**
		 * Get the HTTP client and configure it for the endpoint URI.  Do this each time
		 * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
		 */
		self::getHttpClient()->setUri($this->_uri);
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
	   return self::getHttpClient()->get();
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
	   return self::getHttpClient()->post($data);
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
	   return self::getHttpClient()->put($data);
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
	   return self::getHttpClient()->delete();
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
}

$key = "5f0155b7ee032a68f82befdc12416b1e";
$flickr_key = '381e601d332ab5ce9c25939570cb5c4b';

$client = new Zend_Rest_Client('http://localhost/zendframework/tests/Zend/Services/test.php');
$result =  $client->returnArray()->get();
if ($result->getStatus()) {
	echo $result->foo;
}

$result = $client->sayHello('Davey', 'Day')->get();
if ($result->getStatus()) {
	echo $result;
}

$result = $client->returnBadStatus()->post();
if ($result->getStatus()) {
	var_dump($result);
} else {
	echo $result;
}

$result = $client->someUnknownFunction()->get();
if ($result->getStatus()) {
	var_dump($result);
} else {
	echo $result;
}

/*echo "<br />";

$client = new Zend_Rest_Client('http://api.flickr.com/services/rest/');
echo $client->method('flickr.test.echo')->name('Davey Shafik')->api_key($flickr_key)->get()->name;

echo "<br />";

$technorati = new Zend_Rest_Client('http://api.technorati.com/bloginfo');
$technorati->key($key);
$technorati->url('http://pixelated-dreams.com');
$result = $technorati->get();
echo $result->firstname .' '. $result->lastname;*/