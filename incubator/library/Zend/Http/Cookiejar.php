<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE,
 * and is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not
 * receive a copy of the Zend Framework license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@zend.com so we can mail you a copy immediately.
 *
 * @package    Zend_Http
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

require_once "Zend.php";
require_once "Zend/Uri.php";
require_once "Zend/Http/Cookie.php";
require_once "Zend/Http/Exception.php";

/**
 * A Zend_Http_Cookiejar object is designed to contain and maintain HTTP cookies, and should
 * be used along with Zend_Http_Client in order to manage cookies across HTTP requests and 
 * responses. 
 * 
 * The class contains an array of Zend_Http_Cookie objects. Cookies can be added and removed 
 * from this array in various ways. The jar can also be useful in returning only the cookies 
 * needed for a specific HTTP request. 
 * 
 * A special parameter can be passed to all methods of this class that return cookies: Cookies 
 * can be returned either in their native form (as Zend_Http_Cookie objects) or as strings - 
 * the later is suitable for sending as the value of the "Cookie" header in an HTTP request. 
 * You can also choose, when returning more than one cookie, whether to get an array of strings
 * (by passing Zend_Http_Cookiejar::COOKIE_STRING_ARRAY) or one unified string for all cookies
 * (by passing Zend_Http_Cookiejar::COOKIE_STRING_CONCAT).
 * 
 * See http://wp.netscape.com/newsref/std/cookie_spec.html for some specs.
 *
 */
class Zend_Http_Cookiejar
{
    /**
     * Return cookie(s) as a Zend_Http_Cookie object
     *
     */
    const COOKIE_OBJECT = 0;
    
    /**
     * Return cookie(s) as a string (suitable for sending in an HTTP request)
     *
     */
    const COOKIE_STRING_ARRAY = 1;
    
    /**
     * Return all cookies as one long string (suitable for sending in an HTTP request)
     *
     */
    const COOKIE_STRING_CONCAT = 2;
    
    /**
     * Array storing cookies
     * 
     * Cookies are stored according to domain and path:
     * $cookies
     *  + www.mydomain.com
     *    + /
     *      - cookie1 
     *      - cookie2
     *    + /somepath
     *      - othercookie
     *  + www.otherdomain.net
     *    + /
     *      - alsocookie
     *
     * @var array
     */
    protected $cookies = array();
    
    /**
     * Construct a new CookieJar object
     *
     */
    public function __construct()
    { }
    
    /**
     * Add a cookie to the jar. Cookie should be passed either as a Zend_Http_Cookie object
     * or as a string - in which case an object is created from the string.
     *
     * @param Zend_Http_Cookie|string $cookie
     * @param Zend_Uri_Http|string $red_uri Optional reference URI (for domain, path, secure)
     */
    public function addCookie($cookie, $ref_uri = null)
    {
        if (is_string($cookie)) {
            $cookie = Zend_Http_Cookie::factory($cookie, $ref_uri);
        }
        
        if ($cookie instanceof Zend_Http_Cookie) {
            $domain = $cookie->getDomain();
            $path = $cookie->getPath();
            if (! isset($this->cookies[$domain])) $this->cookies[$domain] = array();
            if (! isset($this->cookies[$domain][$path])) $this->cookies[$domain][$path] = array();
            $this->cookies[$domain][$path][$cookie->getName()] = $cookie;
        } else {
            throw new Zend_Http_Exception('Supplient argument is not a valid cookie string or object');
        }
    }
    
    /**
     * Parse an HTTP response, adding all the cookies set in that response
     * to the cookie jar.
     *
     * @param Zend_Http_Response $response
     * @param Zend_Uri_Http|string $ref_uri Requested URI
     */
    public function addCookiesFromResponse($response, $ref_uri)
    {
        $cookie_hdrs = $response->getHeader('Set-Cookie');
        
        if (is_array($cookie_hdrs)) {
            foreach ($cookie_hdrs as $cookie) {
                $this->addCookie($cookie, $ref_uri);
            }
        } elseif (is_string($cookie_hdrs)) {
            $this->addCookie($cookie_hdrs, $ref_uri);
        }
    }
    
    /**
     * Get all cookies in the cookie jar as an array
     *
     * @param int $ret_as Whether to return cookies as objects of Zend_Http_Cookie or as strings
     * @return array|string
     */
    public function getAllCookies($ret_as = self::COOKIE_OBJECT)
    {
        $cookies = $this->_flattenCookiesArray($this->cookies, $ret_as);
        return $cookies;
    }
    
    /**
     * Return an array of all cookies matching a specific request according to the request URI,
     * whether session cookies should be sent or not, and the time to consider as "now" when
     * checking cookie expiry time.
     *
     * @param string|Zend_Uri_Http $uri URI to check against (secure, domain, path)
     * @param boolean $matchSessionCookies Whether to send session cookies
     * @param int $ret_as Whether to return cookies as objects of Zend_Http_Cookie or as strings
     * @param int $now Override the current time when checking for expiry time
     * @return array|string
     */
    public function getMatchingCookies($uri, $matchSessionCookies = true, 
        $ret_as = self::COOKIE_OBJECT, $now = null)
    {
        if (is_string($uri)) $uri = Zend_Uri_Http::factory($uri);
        if (! $uri instanceof Zend_Uri_Http) {
            throw new Zend_Http_Exception("Invalid URI: {$uri}");
        }
        
        // First, reduce the array of cookies to only those matching domain and path
        $cookies = $this->_matchDomain($uri->getHost());
        $cookies = $this->_matchPath($cookies, dirname($uri->getPath()));
        $cookies = $this->_flattenCookiesArray($cookies, self::COOKIE_OBJECT);
        
        // Next, run Cookie->match on all cookies to check secure, time and session mathcing
        $ret = array();
        foreach ($cookies as $cookie) {
            if ($cookie->match($uri, $matchSessionCookies, $now)) {
                $ret[] = $cookie;
            }
        }
        
        // Now, use self::_flattenCookiesArray again - only to convert to the return format ;)
        $ret = $this->_flattenCookiesArray($cookies, $ret_as);
        
        return $ret;
    }
    
    /**
     * Get a specific cookie according to a URI and name
     *
     * @param Zend_Uri_Http|string $uri The uri (domain and path) to match
     * @param string $cookie_name The cookie's name
     * @param int $ret_as Whether to return cookies as objects of Zend_Http_Cookie or as strings
     * @return Zend_Http_Cookie|string
     */
    public function getCookie($uri, $cookie_name, $ret_as = self::COOKIE_OBJECT)
    {
        if (is_string($uri)) {
            $uri = Zend_Uri_Http::factory($uri);
        }
        
        if (! $uri instanceof Zend_Uri_Http) {
            throw new Zend_Http_Exception('Invalid URI specified');
        }
        
        if (isset($this->cookies[$uri->getHost()][dirname($uri->getPath())][$cookie_name])) {
            $cookie = $this->cookies[$uri->getHost()][dirname($uri->getPath())][$cookie_name];
            
            switch ($ret_as) {
                case self::COOKIE_OBJECT:
                    return $cookie;
                    break;
                    
                case self::COOKIE_STRING_ARRAY:
                case self::COOKIE_STRING_CONCAT:
                    return $cookie->asString();
                    break;
                    
                default:
                    throw new Zend_Http_Exception("Invalid value passed for \$ret_as: {$ret_as}");
                    break;
            }
        } else {
            return false;
        }
    }        
    
    /**
     * Remove all cookies from the jar
     *
     */
    public function deleteAllCookies()
    {
        unset($this->cookies);
        $this->cookies = new ArrayObject();
    }
    
    /**
     * Clear all cookies who's expiry time is older than $time
     *
     * @param int $time Expiry time (default is now)
     */
    public function deleteExpiredCookies($time = null)
    {
        if ($time === null) $time = time();
        $cookies = $this->_flattenCookiesArray($this->cookies, self::COOKIE_OBJECT);
        
        foreach ($cookies as $cookie) {
            if ($cookie->isExpired($time))
                unset($this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()]);
        }
    }
    
    /**
     * Clear "Session" cookies (cookies without specific expiry time)
     *
     */
    public function deleteSessionCookies()
    {
        $cookies = $this->_flattenCookiesArray($this->cookies, self::COOKIE_OBJECT);
        
        foreach ($cookies as $cookie) {
            if ($cookie->isSessionCookie())
                unset($this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()]);
        }
    }
    
    /**
     * Delete a cookie according to it's name and domain. If no name is specified,
     * all cookies from this domain will be cleared out.
     *
     * @param string|Zend_Uri_Http $domain
     * @param string $cookie_name 
     * @return boolean true if cookie was deleted.
     */
    public function deleteCookies($domain, $cookie_name = null)
    {
        $ret = false;
        $path = '/';
        if ($domain instanceof Zend_Uri_Http) {
            $path = dirname($domain->getPath());
            $domain = $domain->getHost();
        } elseif (is_string($domain) && Zend_Uri_Http::check($domain)) {
            $domain = Zend_Uri_Http::factory($domain);
            $path = dirname($domain->getPath());
            $domain = $domain->getHost();
        }
        
        // If we have a cookie's name, delete only this one
        if (isset($cookie_name) && isset($this->cookies[$domain][$path][$cookie_name])) {
            unset($this->cookies[$domain][$path][$cookie_name]);
            $ret = true;
            
        // If we only got a URI, clear all cookies matching this URI.    
        } else {
            $cookies = $this->_matchPath($this->_matchDomain($domain), $path);
            foreach ($cookies as $cookie) {
                if (isset($this->cookies[$cookie->getDomain()][$cookie->getPath])) {
                    unset($this->cookies[$cookie->getDomain()][$cookie->getPath]);
                    $ret = true;
                    if (count ($this->cookies[$cookie->getDomain()]) == 0)
                        unset($this->cookies[$cookie->getDomain()]);
                }
            }
        }
        
        return $ret;
    }
    
    /**
     * Helper function to recursivly flatten an array. Shoud be used when exporting the 
     * cookies array (or parts of it)
     *
     * @param Zend_Http_Cookie|array $ptr
     * @param int $ret_as What value to return
     * @return array|string
     */
    protected function _flattenCookiesArray($ptr, $ret_as = self::COOKIE_OBJECT) {
        if (is_array($ptr)) {
            $ret = ($ret_as == self::COOKIE_STRING_CONCAT ? '' : array());
            foreach ($ptr as $item) {
                if ($ret_as == self::COOKIE_STRING_CONCAT) {
                    $ret .= $this->_flattenCookiesArray($item, $ret_as);
                } else {
                    $ret = array_merge($ret, $this->_flattenCookiesArray($item, $ret_as));
                }
            }
            return $ret;
        } elseif ($ptr instanceof Zend_Http_Cookie) {
            switch ($ret_as) {
                case self::COOKIE_OBJECT:
                    return array($ptr);
                    break;
                
                case self::COOKIE_STRING_ARRAY:
                    return array($ptr->asString());
                    break;
                    
                case self::COOKIE_STRING_CONCAT:
                    return $ptr->asString();
                    break;
                    
                default:
                    throw new Zend_Http_Exception("Invalid value passed for \$ret_as: {$ret_as}");
            }
        }
    }
    
    /**
     * Return a subset of the cookies array matching a specific domain
     * 
     * Returned array is actually an array of pointers to items in the $this->cookies array.
     *
     * @param string $domain
     * @return array
     */
    protected function _matchDomain($domain) {
        $ret = array();
        
        foreach (array_keys($this->cookies) as $cdom) {
            $regex = "/" . preg_quote($cdom, "/") . "$/i";
            if (preg_match($regex, $domain)) $ret[$cdom] = &$this->cookies[$cdom];
        }
        
        return $ret;
    }
    
    /**
     * Return a subset of a domain-matching cookies that also match a specified path
     *
     * Returned array is actually an array of pointers to items in the $passed array.
     * 
     * @param array $dom_array
     * @param string $path
     * @return array
     */
    protected function _matchPath($domains, $path) {
        $ret = array();
        
        foreach ($domains as $dom => $paths_array) {
            foreach (array_keys($paths_array) as $cpath) {
                $regex = "|^" . preg_quote($cpath, "|") . "|i";
                if (preg_match($regex, $path)) {
                    if (! isset($ret[$dom])) $ret[$dom] = array();
                    $ret[$dom][$cpath] = &$paths_array[$cpath];
                }
            }
        }
        
        return $ret;
    }
    
    /**
     * Create a new CookieJar object and automatically load into it all the 
     * cookies set in an Http_Response object. If $uri is set, it will be 
     * considered as the requested URI for setting default domain and path
     * of the cookie.
     *
     * @param Zend_Http_Response $response HTTP Response object
     * @param Zend_Uri_Http|string $uri The requested URI 
     * @return Zend_Http_Cookiejar
     * @todo Add the $uri functionality. 
     */
    static public function factory(Zend_Http_Response $response, $ref_uri)
    {
        $jar = new Zend_Http_Cookiejar();
        $jar->addCookiesFromResponse($response, $ref_uri);
        return $jar;
    }
}