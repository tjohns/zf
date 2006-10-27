<?php 
 
/** Zend_Request_Interface */ 
require_once 'Zend/Request/Interface.php'; 

/** Zend_Http_Exception */ 
require_once 'Zend/Http/Exception.php'; 

/** Zend_Uri */ 
require_once 'Zend/Uri.php'; 
 
/**
 * Zend_Http_Request 
 * 
 * @uses Zend_Request_Interface
 * @package Zend_Http
 * @subpackage Request
 */
class Zend_Http_Request implements Zend_Request_Interface 
{ 
    /**
     * REQUEST_URI
     * @var string;
     */
    protected $_requestUri; 

    /**
     * Base URL of request
     * @var string
     */
    protected $_baseUrl = ''; 

    /**
     * Base path of request
     * @var string
     */
    protected $_basePath = ''; 

    /**
     * PATH_INFO
     * @var string
     */
    protected $_pathInfo = ''; 

    /**
     * Instance parameters
     * @var array
     */
    protected $_params = array(); 

    /**
     * Alias keys for request parameters
     * @var array
     */
    protected $_aliases = array(); 

    /**
     * Constructor
     *
     * If a $uri is passed, the object will attempt to populate itself using 
     * that information.
     * 
     * @param string|Zend_Uri $uri 
     * @return void
     */
    public function __construct($uri = null)
    {
        if (null !== $uri) {
            if (!$uri instanceof Zend_Uri) {
                $uri = Zend_Uri::factory($uri);
            } 
            if ($uri->valid()) {
                $path  = $uri->getPath();
                $query = $uri->getQuery();
                if (!empty($query)) {
                    $path .= '?' . $query;
                }

                $this->setRequestUri($path);
            }
        }
    }
     
    /**
     * Access values contained in the superglobals as public members
     * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV
     * 
     * @see http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     * @param string $key
     * @return mixed
     */ 
    public function __get($key) 
    { 
        switch (true) {
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            case isset($_SERVER[$key]):
                switch ($key) {
                    case 'REQUEST_URI':
                        return $this->getRequestUri();
                    case 'PATH_INFO':
                        return $this->getPathInfo();
                    default:
                        return $_SERVER[$key];
                }
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return null;
        }
    } 

    /**
     * Alias to __get
     * 
     * @param string $key 
     * @return mixed
     */
    public function get($key)
    {
        return $this->__get($key);
    }

    /**
     * Set values
     *
     * In order to follow {@link __get()}, which operates on a number of 
     * superglobals, setting values through overloading is not allowed and will 
     * raise an exception. Use setParam() instead.
     * 
     * @param string $key 
     * @param mixed $value 
     * @return void
     * @throws Zend_Http_Exception
     */
    public function __set($key, $value)
    {
        throw new Zend_Http_Exception('Setting values in superglobals not allowed; please use setParam()');
    }

    /**
     * Alias to __set()
     * 
     * @param string $key 
     * @param mixed $value 
     * @return unknown
     */
    public function set($key, $value)
    {
        return $this->__set($key, $value);
    }

    /**
     * Check to see if a property is set
     * 
     * @param string $key 
     * @return boolean
     */
    public function __isset($key)
    {
        switch (true) {
            case isset($_GET[$key]):
                return true;
            case isset($_POST[$key]):
                return true;
            case isset($_COOKIE[$key]):
                return true;
            case isset($_SERVER[$key]):
                return true;
            case isset($_ENV[$key]):
                return true;
            default:
                return false;
        }
    }

    /**
     * Alias to __isset()
     * 
     * @param string $key 
     * @return boolean
     */
    public function has($key)
    {
        return $this->__isset($key);
    }
     
    /**
     * Retrieve a member of the $_GET superglobal
     * 
     * @todo How to retrieve from nested arrays
     * @param string $key 
     * @return mixed Returns null if key does not exist
     */
    public function getQuery($key) 
    { 
        return (isset($_GET[$key])) ? $_GET[$key] : null; 
    } 
     
    /**
     * Retrieve a member of the $_POST superglobal
     * 
     * @todo How to retrieve from nested arrays
     * @param string $key 
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key) 
    { 
        return (isset($_POST[$key])) ? $_POST[$key] : null; 
    } 
     
    /**
     * Retrieve a member of the $_COOKIE superglobal
     * 
     * @todo How to retrieve from nested arrays
     * @param string $key 
     * @return mixed Returns null if key does not exist
     */
    public function getCookie($key) 
    { 
        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : null; 
    } 
     
    /**
     * Retrieve a member of the $_SERVER superglobal
     * 
     * @param string $key 
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key) 
    { 
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : null; 
    } 
     
    /**
     * Retrieve a member of the $_ENV superglobal
     * 
     * @param string $key 
     * @return mixed Returns null if key does not exist
     */
    public function getEnv($key) 
    { 
        return (isset($_ENV[$key])) ? $_ENV[$key] : null; 
    } 
     
    /**
     * Set the REQUEST_URI on which the instance operates
     *
     * If no request URI is passed, uses the value in $_SERVER or $_SERVER['HTTP_X_REWRITE_URL']
     * 
     * @param string $requestUri 
     * @return void|false Returns false if no $requestUri provided and unable to 
     * find in $_SERVER
     */
    public function setRequestUri($requestUri = null) 
    { 
        if ($requestUri === null) { 
            if (isset($_SERVER['REQUEST_URI'])) { 
                $requestUri = $_SERVER['REQUEST_URI']; 
            } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) { 
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL']; 
            } else { 
                return false; 
            } 
        } elseif (!is_string($requestUri)) {
            return false;
        } else {
            // Set GET items, if available
            $_GET = array();
            if (false !== ($pos = strpos($requestUri, '?'))) {
                $query = substr($requestUri, $pos + 1);

                // strip off anchor fragment
                if (false !== ($pos = strpos($query, '#'))) {
                    $query = substr($query, 0, $pos);
                }

                // Normalize ampersands
                $query = str_replace('&amp;', '&', $query);

                // Get key => value pairs and set $_GET
                $pairs = explode('&', $query);
                foreach ($pairs as $pair) {
                    // list() would be better, but throws a notice if there 
                    // aren't the same number of elements
                    $item = explode('=', $pair, 2);
                    if (!isset($item[1])) {
                        $item[1] = null;
                    }
                    $_GET[$item[0]] = urldecode($item[1]);
                }
            }
        }
         
        $this->_requestUri = $requestUri; 
        $this->setPathInfo();
    } 
     
    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @return string
     */ 
    public function getRequestUri() 
    { 
        if ($this->_requestUri === null) { 
            $this->setRequestUri(); 
        } 
         
        return $this->_requestUri; 
    } 
     
    /**
     * Set the base URL of the request; i.e., the segment leading to the script name
     *
     * If a boolean true $baseUrl is provided, attempts to determine the base 
     * URL from the environment, using SCRIPT_FILENAME, SCRIPT_NAME, PHP_SELF, 
     * and ORIG_SCRIPT_NAME in its determination.
     * 
     * @param mixed $baseUrl 
     * @return void|false Returns false if no $baseUrl provided and unable to 
     * determine baseUrl from environment
     */
    public function setBaseUrl($baseUrl = null) 
    { 
        if ((null !== $baseUrl) && !is_string($baseUrl)) {
            return false;
        }

        if ($baseUrl === null) { 
            $filename = basename($_SERVER['SCRIPT_FILENAME']); 
             
            if (basename($_SERVER['SCRIPT_NAME']) === $filename) { 
                $baseUrl = $_SERVER['SCRIPT_NAME']; 
            } elseif (basename($_SERVER['PHP_SELF']) === $filename) { 
                $baseUrl = $_SERVER['PHP_SELF']; 
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) { 
                $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility 
            } else { 
                // Backtrack up the script_filename to find the portion matching 
                // php_self
                $path    = $_SERVER['PHP_SELF'];
                if (false !== ($pos = strpos($path, '?'))) {
                    $path = (substr($path, 0, $pos));
                }
                $segs    = explode('/', trim($filename, '/'));
                $index   = $count($segs) - 1;
                $baseUrl = ' ';
                do {
                    $last = $segs[$index];
                    $baseUrl = '/' . $last . $baseUrl;
                    --$index;
                } while ((-1 < $index) && (false !== ($pos = strpos($path, $last))) && (0 != $pos));

                if ('' == $baseUrl) {
                    return false; 
                }
            } 
             
            if (null === ($requestUri = $this->getRequestUri())) { 
                return false; 
            } 
             
            // If using mod_rewrite or ISAPI_Rewrite strip the script filename 
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value 
            // from PATH_INFO or QUERY_STRING 
            if ((false === ($pos = strpos($requestUri, $baseUrl))) || ($pos !== 0)) { 
                $baseUrl = dirname($baseUrl); 
            } 
        } 
         
        $this->_baseUrl = rtrim($baseUrl, '/'); 
        $this->setPathInfo();
    } 
 
    /**
     * Everything in REQUEST_URI before PATH_INFO
     * <form action="<?=$baseUrl?>/news/submit" method="POST"/>
     *
     * @return string
     */ 
    public function getBaseUrl() 
    { 
        if ($this->_baseUrl === null) { 
            $this->setBaseUrl(); 
        } 
         
        return $this->_baseUrl; 
    } 
     
    /**
     * Set the base path for the URL
     * 
     * @param string|null $basePath 
     * @return void|false Returns false if no $basePath provided and unable to 
     * determine from environment
     */
    public function setBasePath($basePath = null) 
    { 
        if ($basePath === null) { 
            $filename = basename($_SERVER['SCRIPT_FILENAME']); 
             
            if (null === ($baseUrl = $this->getBaseUrl())) { 
                return false; 
            } 
             
            if (basename($baseUrl) === $filename) { 
                $basePath = dirname($baseUrl); 
            } else { 
                $basePath = $baseUrl; 
            } 
        } 
             
        $this->_basePath = rtrim($basePath, '/'); 
    } 
     
    /**
     * Everything in REQUEST_URI before PATH_INFO not including the filename
     * <img src="<?=$basePath?>/images/zend.png"/>
     *
     * @return string
     */ 
    public function getBasePath() 
    { 
        if ($this->_basePath === null) { 
            $this->setBasePath(); 
        } 
         
        return $this->_basePath; 
    } 
     
    /**
     * Set the PATH_INFO string
     * 
     * @param string|null $pathInfo 
     * @return void|false Returns false if no $pathInfo provided and unable to 
     * determine it from the environment
     */
    public function setPathInfo($pathInfo = null) 
    { 
        if ($pathInfo === null) { 
            $baseUrl = $this->getBaseUrl();
             
            if (null === ($requestUri = $this->getRequestUri())) { 
                return false; 
            } 
             
            // Remove the query string from REQUEST_URI 
            if ($pos = strpos($requestUri, '?')) { 
                $requestUri = substr($requestUri, 0, $pos); 
            } 
             
            if ((null !== $baseUrl)
                && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) 
            { 
                // If substr() returns false then PATH_INFO is set to an empty string 
                $pathInfo = ''; 
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }
        } 
         
        $this->_pathInfo = (string) $pathInfo; 
    } 
 
    /**
     * Returns everything between the BaseUrl and QueryString.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */ 
    public function getPathInfo() 
    { 
        if ($this->_pathInfo === null) { 
            $this->setPathInfo(); 
        } 
         
        return $this->_pathInfo; 
    } 
     
    /**
     * Set a userland parameter
     *
     * Uses $key to set a userland parameter. If $key is an alias, the actual 
     * key will be retrieved and used to set the parameter.
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @return void
     */
    public function setParam($key, $value) 
    { 
        $keyName = (null !== ($alias = $this->getAlias($key))) ? $alias : $key; 
        $this->_params[$keyName] = $value; 
    } 
     
    /**
     * Retrieve a parameter
     *
     * Retrieves a parameter from the instance. Priority is in the order of 
     * userland parameters (see {@link setParam()}), $_GET, $_POST. If a 
     * parameter matching the $key is not found, null is returned.
     *
     * If the $key is an alias, the actual key aliased will be used.
     * 
     * @param mixed $key 
     * @return mixed
     */
    public function getParam($key) 
    { 
        $keyName = (null !== ($alias = $this->getAlias($key))) ? $alias : $key; 
         
        if (isset($this->_params[$keyName])) { 
            return $this->_params[$keyName]; 
        } elseif ((isset($_GET[$keyName]))) { 
            return $_GET[$keyName]; 
        } elseif ((isset($_POST[$keyName]))) { 
            return $_POST[$keyName]; 
        } 
         
        return null; 
    } 
     
    /**
     * Retrieve an array of parameters
     *
     * Retrieves a merged array of parameters, with precedence of userland 
     * params (see {@link setParam()}), $_GET, $POST (i.e., values in the 
     * userland params will take precedence over all others).
     * 
     * @return array
     */
    public function getParams() 
    { 
        return $this->_params + $_GET + $_POST; 
    } 
     
    /**
     * Set parameters
     * 
     * Set one or more parameters. Parameters are set as userland parameters, 
     * using the keys specified in the array.
     * 
     * @param array $params 
     */
    public function setParams(array $params) 
    { 
        foreach ($params as $key => $value) { 
            $this->setParam($key, $value); 
        } 
    } 
     
    /**
     * Set a key alias
     *
     * Set an alias used for key lookups. $name specifies the alias, $target 
     * specifies the actual key to use.
     * 
     * @param string $name 
     * @param string $target 
     * @return void
     */
    public function setAlias($name, $target) 
    { 
        $this->_aliases[$name] = $target; 
    } 
     
    /**
     * Retrieve an alias
     *
     * Retrieve the actual key represented by the alias $name.
     * 
     * @param string $name 
     * @return string|null Returns null when no alias exists
     */
    public function getAlias($name) 
    { 
        if (isset($this->_aliases[$name])) { 
            return $this->_aliases[$name]; 
        } 
         
        return null; 
    } 
     
    /**
     * Retrieve the list of all aliases
     * 
     * @return array
     */
    public function getAliases() 
    { 
        return $this->_aliases; 
    } 

    /**
     * Return the method by which the request was made
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }

    /**
     * Was the request made by POST?
     * 
     * @return boolean
     */
    public function isPost()
    {
        if ('POST' == $this->getMethod()) {
            return true;
        }

        return false;
    }
}
