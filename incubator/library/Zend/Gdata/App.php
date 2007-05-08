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
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Gdata_App_Exception
 */
require_once 'Zend/Gdata/App/Exception.php';

/**
 * Zend_Gdata_Feed
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * Zend_Gdata_App_HttpException
 */
require_once 'Zend/Gdata/App/HttpException.php';

/**
 * Zend_Gdata_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Zend_Gdata_App_InvalidArgumentException
 */
require_once 'Zend/Gdata/App/InvalidArgumentException.php';

/**
 * Provides Atom Publishing Protocol (APP) functionality.  This class and all
 * other components of Zend_Gdata_App are designed to work independently from
 * other Zend_Gdata components in order to interact with generic APP services.
 *
 * @category   Zend
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App
{

    /**
     * Client object used to communicate
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient;

    /**
     * Client object used to communicate in static context
     *
     * @var Zend_Http_Client
     */
    protected static $_staticHttpClient = null;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     */
    protected static $_httpMethodOverride = false;

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     */
    public function __construct($client = null)
    {
        $this->setHttpClient($client);
    }

    /**
     * Retreive feed object
     *
     * @param string $uri
     * @return Zend_Gdata_App_Feed
     */
    public function getFeed($uri, $className='Zend_Gdata_App_Feed')
    {
        $this->_httpClient->resetParameters();
        return $this->import($uri, $this->_httpClient, $className);
    }

    /**
     * Retreive entry object
     *
     * @param string $uri
     * @return Zend_Gdata_App_Entry
     */
    public function getEntry($uri, $className='Zend_Gdata_App_Entry')
    {
        $this->_httpClient->resetParameters();
        return $this->import($uri, $this->_httpClient, $className);
    }

    /**

    /**
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    /**
     * @param Zend_Http_Client $client
     * @throws Zend_Gdata_App_HttpException
     * @return Zend_Gdata_App Provides a fluent interface
     */
    public function setHttpClient($client)
    {
        if ($client == null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            throw new Zend_Gdata_App_HttpException('Argument is not an instance of Zend_Http_Client.');
        }
        $client->setConfig(array('strictredirects' => true));
        $this->_httpClient = $client;
        Zend_Gdata::setStaticHttpClient($client); 
        return $this;
    }


    /**
     * Set the static HTTP client instance
     *
     * Sets the static HTTP client object to use for retrieving the feed.
     *
     * @param  Zend_Http_Client $httpClient
     * @return Zend_Gdata_App_Feed Provides a fluent interface
     */
    public static function setStaticHttpClient(Zend_Http_Client $httpClient)
    {
        self::$_staticHttpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object. If none is set, a new Zend_Http_Client will be used.
     *
     * @return Zend_Http_Client_Abstract
     */
    public static function getStaticHttpClient()
    {
        if (!self::$_staticHttpClient instanceof Zend_Http_Client) {
            /**
             * @see Zend_Http_Client
             */
            require_once 'Zend/Http/Client.php';
            self::$_staticHttpClient = new Zend_Http_Client();
        }
        return self::$_staticHttpClient;
    }

    /**
     * Toggle using POST instead of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value of PUT or
     * DELETE as appropriate.
     *
     * @param  boolean $override Whether to override PUT and DELETE.
     * @return Zend_Gdata_Feed Provides a fluent interface
     */
    public static function setHttpMethodOverride($override = true)
    {
        self::$_httpMethodOverride = $override;
        return $this;
    }


    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getHttpMethodOverride()
    {
        return self::$_httpMethodOverride;
    }

    /**
     * Imports a feed located at $uri.
     *
     * @param  string $uri
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_App_Feed
     */
    public static function import($uri, $client = null, $className='Zend_Gdata_App_Feed')
    {
        $client->setUri($uri);
        $response = $client->request('GET');
        if ($response->getStatus() !== 200) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception('Feed failed to load, got response code ' . $response->getStatus());
        }
        $feedContent = $response->getBody();
        $feed = self::importString($feedContent, $className);
        if ($client != null) {
            $feed->setHttpClient($client);
        } else {
            $feed->setHttpClient(self::getStaticHttpClient());
        }
        return $feed;
    }


    /**
     * Imports a feed represented by $string.
     *
     * @param  string $string
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_App_Feed
     */
    public static function importString($string, $className='Zend_Gdata_App_Feed')
    {
        // Load the feed as an XML DOMDocument object
        @ini_set('track_errors', 1);
        $doc = new DOMDocument();
        $success = @$doc->loadXML($string);
        @ini_restore('track_errors');

        if (!$success) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("DOMDocument cannot parse XML: $php_errormsg");
        }
        $feed = new $className(null, $string);
        $feed->setHttpClient(self::getstaticHttpClient());
        return $feed;
    }


    /**
     * Imports a feed from a file located at $filename.
     *
     * @param  string $filename
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_Feed
     */
    public static function importFile($filename, $className='Zend_Gdata_App_Feed')
    {
        @ini_set('track_errors', 1);
        $feed = @file_get_contents($filename);
        @ini_restore('track_errors');
        if ($feed === false) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("File could not be loaded: $php_errormsg");
        }
        return self::importString($feed, $className);
    }

    /**
     * POST data to Google with authorization headers set
     *
     * @param (string|Zend_Gdata_Event) $data
     * @param string $uri POST URI
     * @return Zend_Http_Response
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     */
    public function post($data, $uri= null)
    {
        if (is_string($data)) {
            $rawData = $data;
        } elseif ($data instanceof Zend_Gdata_App_Entry) {
            $rawData = $data->saveXML();
        } else {
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the data to post as either a string or a child of Zend_Gdata_App_Entry');
        } 
        if ($uri == null) {
            $uri = $this->_defaultPostUri;
        }
        if ($uri == null) {
            throw new Zend_Gdata_App_InvalidArgumentException('You must specify an URI to which to post.');
        }
        $this->_httpClient->setUri($uri);
        $this->_httpClient->setConfig(array('maxredirects' => 0));
        $this->_httpClient->setRawData($rawData,'application/atom+xml');
        try {
            $response = $this->_httpClient->request('POST');
        } catch (Zend_Http_Client_Exception $e) {
            throw new Zend_Gdata_App_HttpException($e->getMessage(), $e);
        }
        /**
         * set "S" cookie to avoid future redirects.
         */
        if($cookie = $response->getHeader('Set-cookie')) {
            list($cookieName, $cookieValue) = explode('=', $cookie, 2);
            $this->_httpClient->setCookie($cookieName, $cookieValue);
        }
        if ($response->isRedirect()) {
            /**
             * Re-POST with redirected URI.
             * This happens frequently.
             */
            $this->_httpClient->setUri($response->getHeader('Location'));
            $this->_httpClient->setRawData($rawData,'application/atom+xml');
            try {
                $response = $this->_httpClient->request('POST');
            } catch (Zend_Http_Client_Exception $e) {
                throw new Zend_Gdata_App_HttpException($e->getMessage(), $e);
            }
        }
        
        if (!$response->isSuccessful()) {
            throw new Zend_Gdata_App_Exception('Post to Google failed. Reason: ' . $response->getBody());
        }
        return $response;
    }

    /**
     * Delete an entry
     *
     * TODO Determine if App should call Entry to Delete or the opposite.  
     * Suspecect opposite would mkae more sense
     * 
     * @param string $data
     * @throws Zend_Gdata_App_HttpException
     */
    public function delete($data)
    {
        if (is_string($data)) {
            $uri = $data;
            $entry = $this->getEntry($uri);
        } elseif ($data instanceof Zend_Gdata_App_Entry) {
            $entry = $data; 
        } else {
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the data to post as either a string or a child of Zend_Gdata_App_Entry');
        } 
        try {
            $entry->delete();
        } catch (Zend_Gdata_App_HttpException $e) {
            throw new Zend_Gdata_App_HttpException($e->getMessage(), $e);
        }
        return true;
    }

    /**
     * Put an entry
     *
     * TODO Determine if App should call Entry to Update or the opposite.  
     * Suspecect opposite would mkae more sense.  Also, this possibly should
     * take an optional URL to override URL used in the entry, or if an
     * edit URI/ID is not present in the entry
     *
     * @param string $data Entry or XML (w/ID and link rel='edit')
     * @throws Zend_Gdata_App_HttpException
     */
    public function put($data)
    {
        try {
            if (is_string($data)) {
                $entry = new Zend_Gdata_App_Entry(null, $data);
                $entry->save();
            } elseif ($data instanceof Zend_Gdata_App_Entry) {
                $data->save();
            } else {
                throw new Zend_Gdata_App_InvalidArgumentException(
                        'You must specify the data to post as either a XML string or a child of Zend_Gdata_App_Entry');
            } 
        } catch (Zend_Gdata_App_HttpException $e) {
            throw new Zend_Gdata_App_HttpException($e->getMessage(), $e);
        }
    }

}
