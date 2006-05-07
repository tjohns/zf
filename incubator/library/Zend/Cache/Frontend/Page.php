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
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
 
/**
 * Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';


/**
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Frontend_Page extends Zend_Cache_Core
{
    const CONTROL_PRIVATE = 0;
    const CONTROL_PUBLIC = 1;
    const CONTROL_FORCED_PUBLIC = 2;
    
    public static $sessionMode = false;
    public static $clientVersion = null;
    
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array(), $doNotTestCacheValidity = false)
    {
        // TODO: refactor options!
        parent::__construct($options);
        return $this->_start($doNotTestCacheValidity);
    }
    
    /**
     * Start the cache
     *
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return boolean true if the cache is hit (false else)
     */
    protected function _start($doNotTestCacheValidity)
    {
        $id = 'something'; // TODO: generate ID here
        
        $data = $this->get($id, $doNotTestCacheValidity);
        if ($data !== false) {
            echo $data;
            return true;
        }
        ob_start(array(__CLASS__, '_flush'));
        ob_implicit_flush(false); // has sense?
        return false;
    }
    
    /**
     * callback for output buffering
     * (shouldn't really be called manually)
     */
    public function _flush($data)
    {
        $this->save($data, null);
        return $data;
    }
    
    /**
     * Simple ETag generation
     * 
     * @param   int     Unix timestamp
     * @return  string  generated ETag
     */
    protected function _ETag($t){
        if(!isset($_SERVER['QUERY_STRING']) && !self::$sessionMode) $etag = $t;
        else {
            $variables = @$_SERVER['QUERY_STRING'];
            if(self::$sessionMode) $variables .= print_r($_SESSION,true).session_name().'='.session_id();
            $etag = md5($t.$variables);
        }
        return '"'.$etag.'"';
    }
    
    /**
     * Formats GMT date according to RFC 1123
     */
    public static function date($t = null, $offset = 0){
        if(is_null($t)) $t = time();
        return substr(gmdate('r', $t + 3600*$offset), 0, -5) . 'GMT';
    }
    
    /**
     * Check for HTTP error 412: precondition failed
     * 
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616.html
     */
    protected static function is412($etag, $timestamp){
        return (
                // rfc2616-sec14.html#sec14.24
                isset($_SERVER['HTTP_IF_MATCH'])
                && ($etags = stripslashes($_SERVER['HTTP_IF_MATCH'])) != '*'
                && strpos($etags, $etag) === false
            ) || (
                // rfc2616-sec14.html#sec14.28
                isset($_SERVER['HTTP_IF_UNMODIFIED_SINCE'])
                && ($ctime = (int) strtotime($_SERVER['HTTP_IF_UNMODIFIED_SINCE'])) > 0
                && (self::$clientVersion = $ctime) < $timestamp
            );
    }
    /**
     * Send correct headers for HTTP error 412: precondition failed
     */
    protected static function send412(){
        // rfc2616-sec10.html#sec10.4.13
        header('HTTP/1.1 412 Precondition Failed');
        //header('Cache-Control: private, max-age=0, must-revalidate');
        header('Content-Type: text/plain; charset=UTF-8');
        echo "HTTP/1.1 Error 412 Precondition Failed:\n";
        echo 'Precondition request failed positive evaluation';
    }
    
    /**
     * Check for HTTP response 304: not modified
     * 
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616.html
     */
    protected static function is304($etag, $timestamp){
        // rfc2616-sec14.html#sec14.26
        if(!isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) return isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
            ( ($etags = stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == '*' || strpos($etags, $etag) !== false );
        
        // rfc2616-sec14.html#sec14.25 and rfc1945.txt
        else return ($ctime = (int) strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])) > 0 &&
            (self::$clientVersion = $ctime) >= $timestamp;
    }
    protected static function send304($etag){
        // rfc2616-sec10.html#sec10.3.5
        header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
        header('ETag: '.$etag);
        header('Connection: close'); //Comment this line under IIS
    }
    
    /**
     * HTTP conditional logic
     * TODO: extract logic and delete this method!
     */
    public function validate($timestamp = 0, $ttl = 0, $cachePrivacy = self::CONTROL_PRIVATE){
        if(headers_sent()) return false;
        
        /**
         * Take script modification time into consideration, but also check that
         * timestamp is not ahead of time
         */
        $timestamp = min(max($timestamp, getlastmod()), time());
        $etag = self::ETag($timestamp);
        
        if(self::is412($etag, $timestamp)){
            self::send412();
            return true;
        }
        if(self::is304($etag, $timestamp)){
            $_SERVER['REQUEST_METHOD'] == 'HEAD' || $_SERVER['REQUEST_METHOD'] == 'GET' ?
                self::send304($etag) : self::send412();
            return true;
        }
        
        // header('HTTP/1.0 200 OK');
        if(!$ttl) $cache = 'private, must-revalidate, ';
        elseif($cachePrivacy == self::CONTROL_PRIVATE) $cache = 'private, ';
        elseif($cachePrivacy == self::CONTROL_FORCED_PUBLIC) $cache = 'public, ';
        else $cache = '';
        $cache .= 'max-age=' . round($ttl);
        
        header('Cache-Control: ' . $cache); // rfc2616-sec14.html#sec14.9
        header('Expires: ' . self::date($timestamp+$ttl)); // HTTP/1.0
        
        self::updateHeaders($timestamp, $etag);
        // if($feedMode) header('Connection: close'); //rfc2616-sec14.html#sec14.10 //Comment this line under IIS
        
        if($_SERVER['REQUEST_METHOD'] == 'HEAD') return true; // rfc2616-sec9.html#sec9.4
        return false;
    }
    
    /**
     * Update HTTP headers if the content has just been modified by the client's request.
     * Note that (according to protocol) GET method should *never* cause data to be changed!
     * 
     * See rfc2616-sec14.html#sec14.21
     * 
     * @param   int $timestamp  new timestamp to update headers with
     */
    public function updateHeaders($timestamp, $etag = null){
        if(headers_sent()) return false;
        if(!$etag) $etag = $this->_ETag($timestamp);
        header('Last-Modified: ' . self::date($timestamp));
        header('ETag: ' . $etag);
        return true;
    }
}

