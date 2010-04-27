<?php

namespace Zend\Cache\Pattern;

class PageCache extends AbstractPattern
{

    protected $_requestMatch     = array();
    protected $_httpStatusCode   = null;
    protected $_httpHeaders      = null;
    protected $_httpDebugHeader  = true;
    protected $_httpEtag         = false;
    protected $_httpEtagSize     = true;
    protected $_httpEtagMtime    = true;
    protected $_httpEtagHash     = false;
    protected $_httpEtagHashAlgo = 'crc32';

    protected $_key = null;

    protected$_cancel = false;

    public function __construct($options)
    {
        // set default http_headers: if cli than false else true
        $this->setHttpHeaders(PHP_SAPI != 'cli');

        parent::__construct($options);
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['requestMatch']     = $this->getRequestMatch();
        $options['httpStatusCode']   = $this->getHttpStatusCode();
        $options['httpHeaders']      = $this->getHttpHeaders();
        $options['httpDebugHeader']  = $this->getHttpDebugHeader();
        $options['httpEtag']         = $this->getHttpEtag();
        $options['httpEtagSize']     = $this->getHttpEtagSize();
        $options['httpEtagMtime']    = $this->getHttpEtagMtime();
        $options['httpEtagHash']     = $this->getHttpEtagHash();
        $options['httpEtagHashAlgo'] = $this->getHttpEtagHashAlgo();
        return $options;
    }

    /**
     * Set request match
     *
     * @param  array $requestMatch
     * @throws Zend\Cache\Exception
     */
    protected function setRequestMatch(array $requestMatch)
    {
        foreach ($requestMatch as $regexp => $spezificRequestOptions) {
            if (!is_array($spezificRequestOptions)) {
                throw new InvalidArgumentException('The request match must be an array of arrays');
            }
        }

        $this->_requestMatch = $requestMatch;
        return $this;
    }

    public function getRequestMatch()
    {
        return $this->_requestMatch;
    }

    public function setHttpHeaders(array $headers)
    {
        if (!is_array($headers) || !count($headers)) {
            $headers = (bool)$headers;
        } else {
            foreach ($headers as &$header) {
                $header = trim(strtolower($header));
            }
            $headers = array_values(array_unique($headers));
        }

        if ($headers && PHP_SAPI == 'cli') {
            throw new RuntimeException("Can't enable http headers in cli mode");
        }

        $this->_httpHeaders = $headers;
        return $this;
    }

    public function getHttpHeaders()
    {
        return $this->_httpHeaders;
    }

    public function setHttpDebugHeader($flag)
    {
        $this->_httpDebugHeader = (bool)$flag;
        return $this;
    }

    public function getHttpDebugHeader()
    {
        return $this->_httpDebugHeader;
    }

    public function setHttpStatusCode($statusCode)
    {
        if ($statusCode) {
            $statusCode = substr($statusCode, 0, 3); // rem status message
            if ((string)(int)$statusCode != $statusCode) {
                throw new InvalidArgumentException("Invalid http status code '{$statusCode}'");
            }
        } else {
            $statusCode = null;
        }

        $this->_httpStatusCode = $statusCode;
        return $this;
    }

    public function getHttpStatusCode()
    {
        return $this->_httpStatusCode;
    }

    public function setHttpEtag($flag)
    {
        $this->_httpEtag = (bool)$flag;
        return $this;
    }

    public function getHttpEtag()
    {
        return $this->_httpEtag;
    }

    public function setHttpEtagSize($flag)
    {
        $this->_httpEtagSize = (bool)$flag;
        return $this;
    }

    public function getHttpEtagSize()
    {
        return $this->_httpEtagSize;
    }

    public function setHttpEtagMtime($flag)
    {
        $this->_httpEtagMtime = (bool)$flag;
        return $this;
    }

    public function getHttpEtagMtime()
    {
        return $this->_httpEtagMtime;
    }

    public function setHttpEtagHash($flag)
    {
        $this->_httpEtagHash = (bool)$flag;
        return $this;
    }

    public function getHttpEtagHash()
    {
        return $this->_httpEtagHash;
    }

    public function setHttpEtagHashAlgo($algo)
    {
        $algo = strtolower($algo);
        if (!in_array($algo, hash_algos())) {
            throw new InvalidArgumentException("Unknown hash algorithm '{$algo}'");
        }

        $this->_httpEtagHashAlgo = $algo;
        return $this;
    }

    public function getHttpEtagHashAlgo()
    {
        return $this->_httpEtagHashAlgo;
    }

    /**
     * Start page cache
     *
     * @param  null|string  $key     The item key or null to autogenerate a key
     * @param  array        $options Cache options
     * @return boolean True if the cache is hit (false else)
     * @throws Zend\Cache\Exception
     */
    public function start($key = null, array $options = array())
    {
        if ($this->_key) {
            throw new RuntimeException('Page cache already started');
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            foreach (array_reverse($this->getRequestMatch()) as $regexp => $cfg) {
                if (preg_match(".{$regexp}.", $_SERVER['REQUEST_URI'])) {
                    $this->setOptions($cfg);
                    break;
                }
            }
        }

        $key = (string)$key;
        if (!isset($key[0])) { // strlen($key) > 0
            $key = $this->_generateKey();
            if (!$key) {
                return false;
            }
        }

        if ( ($cachedArray = $this->getStorage()->get($key)) !== null
          && isset($cachedArray['output'], $cachedArray['headers'], $cachedArray['mtime']) ) {
            // send debug header
            if ($this->getHttpDebugHeader()) {
                header('X-Zend_PageCache: cached response '
                     . @date('r', $cachedArray['mtime']));
            }

            // send cached headers and etag functionality
            if ($this->getHttpHeaders() && !headers_sent()) {

                // send response header code
                // etag functionality can overwrite this
                if (isset($cachedArray['headers'][0])) {
                    $normallyResponseCode = (int)$cachedArray['headers'][0];
                    header('Status: '.$normallyResponseCode, true, $normallyResponseCode);
                    unset($cachedArray['headers'][0]); // remove response code from normal header list
                } else {
                    $normallyResponseCode = 200;
                }

                // handle etag matching
                if (isset($cachedArray['headers']['etag'])) {
                    // TODO: If-Range

                    // "If-None-Match" & "If-Modified-Since"
                    if ( isset($_SERVER['HTTP_IF_NONE_MATCH']) || isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
                      && ($normallyResponseCode >= 200 && $normallyResponseCode < 300) ) {
                        $send304 = null;

                        // "If-None-Match"
                        // @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.26
                        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
                            $send304 = false;
                            $httpIfNoneMatch = trim($_SERVER['HTTP_IF_NONE_MATCH']);
                            if ($httpIfNoneMatch == '*') {
                                $send304 = true;
                            } else {
                                $httpIfNoneMatchList = explode(',', $httpIfNoneMatch);
                                foreach ($httpIfNoneMatchList as $etag) {
                                    $etag = trim($etag);
                                    if ($etag == $cachedArray['headers']['etag']) {
                                        $send304 = true;
                                        break;
                                    }
                                }
                            }
                        }

                        // "If-Modified-Since"
                        // @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.25
                        if ( $send304 === null || $send304 === true
                          && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                            $send304 = false;

                            // The If-Modified-Since header have to match exactly
                            // and only a normally result code of 200 is allowed
                            if ($normallyResponseCode == 200) {
                                if (trim($_SERVER['HTTP_IF_MODIFIED_SINCE']) == gmdate('D, d M Y H:i:s', $cachedArray['mtime']).' GMT') {
                                    $send304 = true;
                                }
                            }
                        }

                        if ($send304 === true) {
                            header('Status: 304', true, 304);
                            if (!isset($options['noexit']) || !$options['noexit']) {
                                exit;
                            }
                        }
                    }

                    // "If-Match" & "If-Unmodified-Since"
                    if (isset($_SERVER['HTTP_IF_UNMODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_MATCH'])
                      && ($normallyResponseCode >= 200 && $normallyResponseCode < 300) ) {
                        $send412 = null;

                        // "If-Match"
                        // @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.24
                        if (isset($_SERVER['HTTP_IF_MATCH'], $cachedArray['headers']['etag'])) {
                            $httpIfMatch = trim($_SERVER['HTTP_IF_MATCH']);
                            if ($httpIfMatch == '*') {
                                $send412 = true;
                            } else {
                                $send412 = true;
                                $httpIfMatchList = explode(',', $httpIfMatch);
                                foreach ($httpIfMatchList as $etag) {
                                    $etag = trim($etag);
                                    if ($etag == $cachedArray['headers']['etag']) {
                                        $send412 = false;
                                        break;
                                    }
                                }
                            }
                        }

                        // "If-Unmodified-Since"
                        // @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.28
                        if ( isset($_SERVER['HTTP_IF_UNMODIFIED_SINCE'])) {
                            $send412 = false;
                            $ifUnmodifiedSinceTs = @strtotime(trim($_SERVER['HTTP_IF_UNMODIFIED_SINCE']));
                            if ($ifUnmodifiedSinceTs && $cachedArray['mtime'] > $ifUnmodifiedSinceTs) {
                                $send412 = true;
                            }
                        }

                        if ($send412 === true) {
                            header('Status: 412', true, 412);
                            if (!isset($options['noexit']) || !$options['noexit']) {
                                exit;
                            }
                        }
                    }

                }

                // send cache headers (after etag)
                foreach ($cachedArray['headers'] as $header) {
                    header($header);
                }
            }

            // send cached data
            echo $cachedArray['output'];

            // exit
            if (isset($options['noexit']) && $options['noexit']) {
                return true;
            }
            exit;
        }

        $this->_key = $key;
        ob_start(array($this, '_flush'));
        ob_implicit_flush(false);
        return false;
    }

    /**
     * Cancel the caching process
     */
    public function cancel()
    {
        $this->_cancel = true;
    }

    /**
     * callback for output buffering
     * (shouldn't really be called manually)
     *
     * @param  string $data Buffered output
     * @return string Data to send to browser
     * @throws Zend_Cache_Exception
     */
    public function _flush($data)
    {
        // caching canceled after start
        if ($this->_cancel) {
            // http://php.net/manual/function.ob-start.php
            // -> If output_callback  returns FALSE original input is sent to the browser.
            return false;
        }

        $now = time();
        $headersToStore = array();
        if ($this->getHttpHeaders()) {
            // store all headers
            $headersSent = array();
            foreach (headers_list() as $i => $headerSent) {
                $tmp = explode(':', $headerSent, 2);
                $headerSentName = strtolower(trim(array_shift($tmp)));
                $headersSent[$headerSentName] = $headerSent;
            }

            $cfgHttpHeaders = $this->getHttpHeaders();
            if (is_array($cfgHttpHeaders)) {
                foreach($cfgHttpHeaders as $headerName) {
                    if (isset($headersSent[$headerName])) {
                        $headersToStore[$headerName] = $headersSent[$headerName];
                    }
                }
            } else {
                $headersToStore = $headersSent;
            }

            // store http status code as 0
            if (isset($headersSent['status'])) {
                $headersToStore[0] = $headersSent['status'];
            } elseif ( ($cfgHttpStatusCode = $this->getHttpStatusCode()) ) {
                $headersToStore[0] = $cfgHttpStatusCode;
            }

            // autogenerate ETag header
            if ($this->getHttpEtag()
              // NOTE: etag header have to be listed in http_headers
              && ( $cfgHttpHeaders === true || isset($cfgHttpHeaders['etag']) )
              // NOTE: Do not generate etag header if etag is already in send list
              && !isset($headersSent['etag'])
              // NOTE: Can't send etag-header if headers are already sent to browser
              && !headers_sent() ) {
                $etag = array();
                if ($this->getHttpEtagSize()) {
                    $etag[] = strlen($data);
                }
                if ($this->getHttpEtagMtime()) {
                    $etag[] = $now;
                }
                if ($this->getHttpEtagHash()) {
                    $etag[] = hash($this->getHttpEtagHashAlgo(), $data);
                }
                $etagHeader = 'ETag: "' . implode('-', $etag) . '"';
                header($etagHeader);
                $headersToStore['etag'] = $etagHeader;
            }
        }

        $this->getStorage()->set(
            array(
                'output'  => $data,
                'headers' => $headersToStore,
                'mtime'   => $now,
            ),
            $this->_key
        );

        // http://php.net/manual/function.ob-start.php
        // -> If output_callback  returns FALSE original input is sent to the browser.
        return false;
    }

    /**
     * Key generator
     *
     * @return string
     */
    protected function _generateKey()
    {
        $key = '';

        if (PHP_SAPI == 'cli') {
            $key.= implode(' ', $_SERVER['argv']);
        } else {
            // add super globals (but _SERVER, _REQUEST)
            foreach (array('_GET', '_POST', '_SESSION', '_FILES', '_COOKIE') as $sgName) {
                if (isset($$sgName)) {
                    $sg = $$sgName;
                    ksort($sg); // "test1=1&test2=2" handled as the same as "test2=2&test1=1"
                    $key.= $sgName . '=' . http_build_query($sg);
                }
            }

            // add special _SERVER keys (if not HTTP_*)
            $specialServerKeys = array(
                // Protocol + Method
                'SERVER_PROTOCOL', 'REQUEST_METHOD',
                // Scheme & Host
                'HTTPS', 'SERVER_NAME', 'SERVER_PORT',
                // Request URI
                'REQUEST_URI', 'ORIG_PATH_INFO',
                'IIS_WasUrlRewritten', 'UNENCODED_URL',
                // 'QUERY_STRING', Query is done by _GET
                // Auth
                'PHP_AUTH_USER'
            );
            foreach ($specialServerKeys as $k => $v) {
                if (isset($_SERVER[$k])) {
                    $key.= '|' . $k . '=' . $v;
                }
            }

            // add ALL http request headers
            foreach ($_SERVER as $k => $v) {
                if (substr($k, 0, 5) == 'HTTP_') {
                    $key.= '|' . $k . '=' . $v;
                }
            }
        }

        return md5($key);
    }

}
