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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata
{

    // Gdata-general request parameters:
    // @todo: request parameter 'q'
    // @todo: request parameter category, e.g. '/feeds/jo/-/Fritz'
    // @todo: request parameter entryId, e.g. '/feeds/jo/entry1
    // @todo: request parameter 'max-results'
    // @todo: request parameter 'start-index'
    // @todo: request parameter 'author'
    // @todo: request parameter 'alt' ('atom' or 'rss')
    // @todo: request parameter 'updated-min'
    // @todo: request parameter 'updated-max'
    // @todo: request parameter 'published-min'
    // @todo: request parameter 'published-max'

    const AUTH_SUB     = 'AuthSub';
    const CLIENT_LOGIN = 'ClientLogin';

    /**
     * Client object used to communicate
     *
     * @var Zend_Http_Client
     */
    protected $client;

    /**
     * Query parameters.
     *
     * @var array
     */
    protected $params = array();

    protected $developerKey = null;

    protected $authMethod;

    protected static $defaultTokenName = 'xapi_token';

    protected static $tokenName = null;

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     */
    public function __construct(Zend_Http_Client $client)
    {
        $this->client = $client;
    }

    /*
    static public function authSub($tokenName = null)
    {
        if ($tokenName == null) {
            $tokenName = self::$defaultTokenName;
        }
        $this->authMethod = Zend_Gdata::AUTH_SUB;
        session_start();
        if (!isset($_SESSION[$tokenName])) {
            if (!isset($_GET['token'])) {
                // display link to generate single-use token
                $authSubUrl = Zend_Gdata_AuthSub::getAuthSubTokenUri('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], $uri, 0, 1);
                return $authSubUrl;
            }
            // convert the single-use token to a session token
            $sessionToken =  Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
            $_SESSION[$tokenName] = $sessionToken;
        }
        $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION[$tokenName]);
        $this->authSubTokenName = $tokenName;
        return $client;
    }

    public static function clientLogin($email, $password, $service = 'xapi')
    {
        echo "Gdata email = $email\n";
        echo "Gdata password = $password\n";
        $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, $service);
        $gdata = self::__construct($client);
        $gdata->authMethod = self::CLIENT_LOGIN;
        return $gdata;
    }
     */

    /**
     * Sets developer key
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->developerKey = substr($key, 0, strcspn($key, "\n\r"));
        $headers['X-Google-Key'] = 'key=' . $this->developerKey;
        $this->client->setHeaders($headers);
    }

    /**
     * @return string querystring
     */
    protected function getQueryString()
    {
        $queryArray = array();
        foreach ($this->params as $name => $value) {
            if (substr($name, 0, 1) == '_') {
                continue;
            }
            $queryArray[] = urlencode($name) . '=' . urlencode($value);
        }
        if (count($queryArray) > 0) {
            return '?' . implode('&', $queryArray);
        } else {
            return '';
        }
    }

    public function resetParameters()
    {
        $this->params = array();
    }

    /**
     * Retreive feed object
     *
     * @param string $uri
     * @return Zend_Feed
     */
    public function getFeed($uri)
    {
        $feed = new Zend_Feed();
        $this->client->resetParameters();
        $feed->setHttpClient($this->client);
        return $feed->import($uri);
    }

    /**
     * POST xml data to Google with authorization headers set
     *
     * @param string $xml
     * @param string $uri POST URI
     * @return Zend_Http_Response
     */
    public function post($xml, $uri)
    {
        $this->client->setUri($uri);
        $this->client->setConfig(array('maxredirects' => 0));
        $this->client->setRawData($xml,'application/atom+xml');
        $response = $this->client->request('POST');
        //set "S" cookie to avoid future redirects.
        if($cookie = $response->getHeader('Set-cookie')) {
            $this->client->setCookie($cookie);
        }
        if ($response->isRedirect()) {
            //this usually happens. Re-POST with redirected URI.
            $this->client->setUri($response->getHeader('Location'));
            $this->client->setRawData($xml,'application/atom+xml');
            $response = $this->client->request('POST');
        }
        
        if (!$response->isSuccessful()) {
            throw Zend::exception('Zend_Gdata_Exception', 'Post to Google failed.');
        }
        return $response;
    }

    /**
     * Delete an entry by its ID uri
     *
     * @param string $uri
     */
    public function delete($uri)
    {
        $feed = $this->getFeed($uri);
        $entry = $feed->current();
        $entry->delete();
        return true;
    }

    protected function __get($var)
    {
        return isset($this->params[$var]) ? $this->params[$var] : null;
    }

    protected function __isset($var)
    {
        return isset($this->params[$var]);
    }

    protected function __unset($var)
    {
        unset($this->params[$var]);
    }

    /**
     * @param string $var
     * @param string $value
     */
    protected function __set($var, $value)
    {
        switch ($var) {
            case 'maxResults':
                $var = 'max-results';
                $value = intval($value);
                break;
            case 'startIndex':
                $var = 'start-index';
                $value = intval($value);
                break;
            case 'updatedMin':
                $var = 'updated-min';
                $value = $this->formatTimestamp($value);
                break;
            case 'updatedMax':
                $var = 'updated-max';
                $value = $this->formatTimestamp($value);
                break;
            case 'publishedMin':
                $var = 'published-min';
                $value = $this->formatTimestamp($value);
                break;
            case 'publishedMax':
                $var = 'published-max';
                $value = $this->formatTimestamp($value);
                break;
            default:
                // other params may be set by subclasses
                break;
        }
        $this->params[$var] = $value;
    }

    /**
     *  Convert timestamp into RFC 3339 date string.
     *  2005-04-19T15:30:00
     *
     * @param int $timestamp
     */
    private function formatTimestamp($timestamp)
    {
        if (ctype_digit($timestamp)) {
            return date('Y-m-d\TH:i:s', $timestamp);
        } else {
            $ts = strtotime($timestamp);
            if ($ts === false) {
                throw Zend::exception('Zend_Gdata_Exception', "Invalid timestamp: $timestamp");
            }
            return date('Y-m-d\TH:i:s', $ts);
        }
    }

}
