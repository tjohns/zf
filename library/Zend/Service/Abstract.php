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
 * @package    Zend_Service
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Service_Exception
 */
require_once 'Zend/Service/Exception.php';

/**
 * Zend_HttpClient
 */
require_once 'Zend/HttpClient.php';


/**
 * @package    Zend_Service
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class Zend_Service_Abstract
{
    /**
     * HTTP Client used to query all web services
     *
     * @var Zend_HttpClient_Abstract
     */
    static protected $_httpClient = null;


    /**
     * Sets the HTTP client object to use for retrieving the feeds.  If none
     * is set, the default Zend_HttpClient will be used.
     *
     * @param Zend_HttpClient_Abstract $httpClient
     */
	final static public function setHttpClient(Zend_HttpClient_Abstract $httpClient)
	{
		self::$_httpClient = $httpClient;
	}


	/**
	 * Gets the HTTP client object.
	 *
	 * @return Zend_HttpClient_Abstract
	 */
	final static public function getHttpClient()
	{
		if (!self::$_httpClient instanceof Zend_HttpClient_Abstract) {
			self::$_httpClient = new Zend_HttpClient();
		}

		return self::$_httpClient;
	}
}

