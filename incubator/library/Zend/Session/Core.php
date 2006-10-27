<?php

/**
 * Zend_Session_Exception
 */
require_once 'Zend/Session/Exception.php';

/**
 * Zend_Session_SaveHandler_Interface
 */
require_once 'Zend/Session/SaveHandler/Interface.php';

/**
 * Zend_Session_Core
 * 
 * @package    Zend_Session_Core
 * @copyright  none applied yet
 * @license    none applied yet
 */
final class Zend_Session_Core
{
    
    /**
     * Check wether or not the session was started
     *
     * @var bool
     */
    protected static $_session_started = false;
    
    /**
     * Instance of Zend_Session_Core
     *
     * @var Zend_Session_Core
     */
    protected static $_instance;

    /**
     * The Singleton enforcer
     *
     * @var bool
     */
    protected static $_singleton = false;
    
    /**
     * Private list of php's ini values for ext/session
     * null values will default to the php.ini value, otherwise
     * the value below will overwrite the default ini value, unless
     * the user has set an option explicity with setOptions()
     *
     * @var array
     */
    protected static $_default_options = array(
        "save_path"                 => null,
        "name"                      => 'ZFSESSION',
        "save_handler"              => null,
        "auto_start"                => null,
        "gc_probability"            => null,
        "gc_divisor"                => null,
        "gc_maxlifetime"            => null,
        "serialize_handler"         => null,
        "cookie_lifetime"           => null,
        "cookie_path"               => null,
        "cookie_domain"             => null,
        "cookie_secure"             => null,
        "use_cookies"               => null,
        "use_only_cookies"          => 'on',
        "referer_check"             => null,
        "entropy_file"              => null,
        "entropy_length"            => null,
        "cache_limiter"             => null,
        "cache_expire"              => null,
        "use_trans_sid"             => null,
        "bug_compat_42"             => null,
        "bug_compat_warn"           => null,
        "hash_function"             => null,
        "hash_bits_per_character"   => null
        );
   
    /**
     * Whether or not write close has been performed.
     *
     * @var bool
     */
    protected static $_write_closed = false;
    
    /**
     * The Logging level of Zend_Session, requires Zend_Log
     *      0 = off
     *      1 = errors
     *      2 = set/get/isset/unset
     *      3 = startup/shutdown values
     * 
     * @var bool
     */
    protected static $_log_level      = 0;
    
    /**
     * Wether or not session must be initiated before usage
     *
     * @var bool
     */
    protected static $_strict          = false;
    
    /**
     * Default number of seconds the session will be remembered for when asked to be remembered
     *
     * @var unknown_type
     */
    protected static $_remember_me_seconds = 1209600; // 2 weeks
    
    /**
     * Wether the default options have been set.
     *
     * @var unknown_type
     */
    protected static $_default_options_set = false;
    
    /**
     * Since expiring data is handled at startup to avoid __destruct difficulties,
     * the data that will be expiring at end of this request is held here
     *
     * @var array
     */
    protected static $_expiring_data = array();
    
    /**
     * Debug mode: primary use for this will be in unit tests where the environment is command
     * line and no headers are exchanged.
     *
     * @var bool
     */
    public static $debug_mode = false;
    
    
    /**
     * SetOptions - set both the class specified
     *
     * @param array $user_options
     */
    public static function setOptions(Array $user_options = array())
    {
        // set default options on first run only (before applying user settings)
        if (!self::$_default_options_set) {
            foreach (self::$_default_options as $default_option_name => $default_option_value) {
                if (isset(self::$_default_options[$default_option_name]) && $default_option_value !== null) {
                    ini_set('session.' . $default_option_name, $default_option_value);
                }
            }
            
            self::$_default_options_set = true;
        }
        
        // set the options the user has requested to set
        foreach ($user_options as $user_option_name => $user_option_value) {
           
            // set the ini based values
            if (array_key_exists($user_option_name, self::$_default_options)) {
                ini_set('session.' . $user_option_name, $user_option_value);
                continue;
            }
            
            // get log level setting if passed
            if ($user_option_name === 'log_level') {
                
                if (!is_int($user_option_value)) {
                    throw new Zend_Session_Exception(__CLASS__ . '::setOptions() log_level expects an integer.');
                }

                if ($user_option_value < 0 || $user_option_value > 3) {
                    throw new Zend_Session_Exception(__CLASS__ . '::setOptions() log_level value is out of range, must be 0 to 5 inclusive.');
                }
                
                if (!class_exists('Zend_Log', false)) {
                    throw new Zend_Session_Exception(__CLASS__ . '::setOptions() logging is enabled, but Zend_Log was not loaded');    
                }
                
                self::$_log_level = $user_option_value;
                continue;
            }
            
            // get strict settting if passed
            if ($user_option_name === 'strict') {
                self::$_strict = $user_option_value;
                continue;
            }

            // get remember me seconds setting if passed
            if ($user_option_name === 'remember_me_seconds') {
                self::$_remember_me_seconds = $user_option_value;
            }

        }
    }
   
    
    /**
     * Session Save Handler assignment
     *
     * @param Zend_Session_SaveHandlerInterface $interface
     * @return void
     */
    public static function setSaveHandler(Zend_Session_SaveHandler_Interface $interface)
    {
        session_set_save_handler(
            array(&$interface, "open"),
            array(&$interface, "close"),
            array(&$interface, "read"),
            array(&$interface, "write"),
            array(&$interface, "destroy"),
            array(&$interface, "gc")
            );
        
        return;
    }
    
    
    /**
     * GetInstance() - Enfore the Singleton of the core.
     *
     * @param boolean $instance_must_exist
     * @return Zend_Session_Core
     */
    public static function getInstance($instance_must_exist = false)
    {
        if (self::$_instance === null && $instance_must_exist === true) {
            throw new Zend_Session_Exception(__CLASS__ . "::getInstance() A valid session must exist before calling getInstance() in this manner.");
        }
        
        if (self::$_instance === null) {
            self::$_singleton = true;
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    
    /**
     * RemoveInstance() - Remove the instance.
     *
     * @return void
     */
    public static function removeInstance()
    {
        self::$_instance = null;
        return;
    }
    

    /**
     * RegenerateId() - Regenerate the session id.
     *
     * @return void
     */
    public static function regenerateId()
    {
        if ( headers_sent($filename, $linenum) && (self::$debug_mode !== true) ) {
            throw new Zend_Session_Exception(__CLASS__ . ": You must call this method before any output has been sent to the browser; output started in {$filename}/{$linenum}");
        }

        session_regenerate_id(true);
        return;
    }
    
    
    /**
     * RememberMe() - Send the remember me cookie, which will (on next request) force the session to resend
     * the session cookie that will expire after a number of seconds in the future (not when the browser closes)
     * Seconds are determined by self::$_remember_me_seconds.
     *
     * @return void
     */
    public static function rememberMe()
    {
        if ( headers_sent($filename, $linenum) && (self::$debug_mode !== true) ) {
            throw new Zend_Session_Exception(__CLASS__ . "::rememberMe() Must be called prior to headers being sent; output started in {$filename}/{$linenum}");
        }
            
        $cookie_params = session_get_cookie_params();
        
        setcookie('REMEMBERME', 'true', time()+(60*60*24*2), $cookie_params['path'], $cookie_params['domain'], $cookie_params['secure']);
        return;
    }
    
    
    /**
     * ForgetMe() - This will make sure to kill the session cookie on the users browser.
     *
     * @return void
     */
    public static function forgetMe()
    {
        if ( headers_sent($filename, $linenum) && (self::$debug_mode !== true) ) {
            throw new Zend_Session_Exception(__CLASS__ . "::forgetMe() Must be called prior to headers being sent; output started in {$filename}/{$linenum}");
        }
        
        $_SESSION = null;
        $cookie_params = session_get_cookie_params();
        setcookie(session_name(), $_COOKIE[session_name()], time()-630720000, $cookie_params['path'], $cookie_params['domain'], $cookie_params['secure']);
        
        return;
    }
    
    
    /**
     * SessionExists() - wether or not a session exist for the current request.
     *
     * @return bool
     */
    public static function sessionExists()
    {
        if (ini_get('session.use_cookies') == "1" && isset($_COOKIE[session_name()])) {
            return true;
        } elseif (isset($_REQUEST[session_name()])) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Start() - Start the session.
     *
     * @return void
     */
    public static function start()
    {
        // make sure our default options (at the least) have been set
        if (!self::$_default_options_set) {
            self::setOptions();
        }
        
        if (headers_sent($filename, $linenum) && self::$debug_mode !== true) {
            throw new Zend_Session_Exception(__CLASS__ . '::start() You must call this method before any output has been sent to the browser; output started in {$filename}/{$linenum}');
        }
            
        if (isset($_COOKIE['REMEMBERME']) && $_COOKIE['REMEMBERME'] == 'true') {
            self::_processRememberMe();
        }
        
        if (self::$_session_started) {
            throw new Zend_Session_Exception(__CLASS__ . '::start() can only be called once.');
        }
        
        session_start();
        self::$_session_started = true;

        // run validators if they exist
        if (isset($_SESSION['__ZF']['VALID'])) {
            self::_processValidators();
        }
        
        if (self::$_log_level == 3) {
            Zend_Log::log('Session startup values before processing:', Zend_Log::LEVEL_DEBUG);
            Zend_Log::log(var_export($_SESSION, true));
        }
        
        self::_processStartupMetadataGlobal();
        
        if (self::$_log_level == 3) {
            Zend_Log::log('Session startup values after processing:', Zend_Log::LEVEL_DEBUG);
            Zend_Log::log(var_export($_SESSION, true));
        }
                
        return;
    }

    
    /**
     * IsStarted() - convenience methods to determine if the session is already started.
     *
     * @return bool
     */
    public static function isStarted()
    {
        return self::$_session_started;
    }
    
    
    /**
     * GetId() - get the current session id
     *
     * @return string
     */
    public static function getId()
    {
        return session_id();
    }
    
    
    /**
     * SetId() - set an id to a user specified id
     *
     * @param string $id
     */
    public static function setId($id)
    {
        if (headers_sent($filename, $linenum) && self::$debug_mode !== true) {
            throw new Zend_Session_Exception(__CLASS__ . "::setId() You must call this method before any output has been sent to the browser.");
        }
        
        if (!is_string($id)) {
            throw new Zend_Session_Exception(__CLASS__ . '::setId() you must provide a string as a session identifier.');
        }
        
        session_id($id);
    }
    
    
    /**
     * RegisterValidator() - register a validator that will attempt to validate this session for
     * every future request
     *
     * @param Zend_Session_Validator_Interface $validator
     */
    public static function registerValidator(Zend_Session_Validator_Interface $validator)
    {
        $validator->setup();
        return;
    }
    
    
    /**
     * Stop() - Convienance method, links to shutdown
     *
     * @return void
     */
    public static function stop()
    {
        self::shutdown();
        return;
    }
    
    
    /**
     * WriteClose() - this will complete the internal data transformation on this request.
     *
     * @return void
     */
    public static function writeClose()
    {
        if (self::$_write_closed) {
            return;
        }
            
        self::$_write_closed = true;
        session_write_close();
        return;
    }
    

    /**
     * Shutdown() - Shutdown the sesssion, close writing and remove the instance
     *
     */
    public static function shutdown()
    {
        self::writeClose();
        self::removeInstance();
        return;
    }

    
    /**
     * _processGlobalMetadata() - this method initizes the sessions GLOBAL 
     * metadata, mostly global data expiration calculations.
     *
     * @return void
     */
    protected static function _processStartupMetadataGlobal()
    {
        // process global metadata
        if (isset($_SESSION['__ZF'])) {
            
            // expire globally expired values
            foreach ($_SESSION['__ZF'] as $namespace => $namespace_metadata) {
                
                // Expire Namespace by Time (ENT)
                if (isset($namespace_metadata['ENT']) && ($namespace_metadata['ENT'] > 0) && (time() > $namespace_metadata['ENT']) ) {
                    unset($_SESSION[$namespace]);
                    unset($_SESSION['__ZF'][$namespace]['ENT']);
                }

                // Expire Namespace by Global Hop (ENGH)
                if (isset($namespace_metadata['ENGH']) && $namespace_metadata['ENGH'] >= 1) {
                    $_SESSION['__ZF'][$namespace]['ENGH']--;
                    
                    if ($_SESSION['__ZF'][$namespace]['ENGH'] === 0) {
                        self::$_expiring_data[$namespace] = $_SESSION[$namespace];
                        unset($_SESSION[$namespace]);
                        unset($_SESSION['__ZF'][$namespace]['ENGH']);
                    }
                }
                                    
                // Expire Namespace Variables by Time (ENVT)
                if (isset($namespace_metadata['ENVT'])) {
                    foreach ($namespace_metadata['ENVT'] as $variable => $time) {
                        if (time() > $time) {
                            unset($_SESSION[$namespace][$variable]);
                            unset($_SESSION['__ZF'][$namespace]['ENVT'][$variable]);
                            
                            if (empty($_SESSION['__ZF'][$namespace]['ENVT'])) {
                                unset($_SESSION['__ZF'][$namespace]['ENVT']);
                            }
                        }
                    }
                }
                    
                // Expire Namespace Variables by Global Hop (ENVGH)
                if (isset($namespace_metadata['ENVGH'])) {
                    foreach ($namespace_metadata['ENVGH'] as $variable => $hops) {
                        $_SESSION['__ZF'][$namespace]['ENVGH'][$variable]--;
                        
                        if ($_SESSION['__ZF'][$namespace]['ENVGH'][$variable] === 0) {
                            self::$_expiring_data[$namespace][$variable] = $_SESSION[$namespace][$variable];
                            unset($_SESSION[$namespace][$variable]);
                            unset($_SESSION['__ZF'][$namespace]['ENVGH'][$variable]);
                        }
                    }
                }
            }
            
            if (empty($_SESSION['__ZF'][$namespace])) {
                unset($_SESSION['__ZF'][$namespace]);
            }
            
        }
        
        if (empty($_SESSION['__ZF'])) {
            unset($_SESSION['__ZF']);
        }
        
    }
    
    
    /**
     * _processStartupMetadataNamespace() - this method processes the metadata specific only
     * to a given namespace.  This is typically run at the instantiation of a Zend_Session object.
     *
     * @param string $namespace
     */
    public static function _processStartupMetadataNamespace($namespace)
    {
        if (!isset($_SESSION['__ZF'])) {
            return;
        }
        
        if (isset($_SESSION['__ZF'][$namespace])) {
            
            // Expire Namespace by Namespace Hop (ENNH)
            if (isset($_SESSION['__ZF'][$namespace]['ENNH'])) {
                $_SESSION['__ZF'][$namespace]['ENNH']--;
                
                if ($_SESSION['__ZF'][$namespace]['ENNH'] === 0) {
                    self::$_expiring_data[$namespace] = $_SESSION[$namespace];
                    unset($_SESSION[$namespace]);
                    unset($_SESSION['__ZF'][$namespace]['ENNH']);
                }
            }
            
            // Expire Namespace Variables by Namespace Hop (ENVNH)
            if (isset($_SESSION['__ZF'][$namespace]['ENVNH'])) {
                foreach ($_SESSION['__ZF'][$namespace]['ENVNH'] as $variable => $hops) {
                    $_SESSION['__ZF'][$namespace]['ENVNH'][$variable]--;
                    
                    if ($_SESSION['__ZF'][$namespace]['ENVNH'][$variable] === 0) {
                        self::$_expiring_data = $_SESSION[$namespace][$variable];
                        unset($_SESSION[$namespace][$variable]);
                        unset($_SESSION['__ZF'][$namespace]['ENVNH'][$variable]);
                    }
                }
            }
        }
        
        if (empty($_SESSION['__ZF'][$namespace])) {
            unset($_SESSION['__ZF'][$namespace]);
        }
            
        if (empty($_SESSION['__ZF'])) {
            unset($_SESSION['__ZF']);
        }
        
    }
    
    
    /**
     * _processRememberMe() - this method handles the process of making the current session
     * cookie extend past the closing of the browser.  The session based cookie will become
     * a time based cookie, expiration will be set into the future (the value specified by
     * self::$_remember_me_seconds).
     *
     * @return void
     */
    protected static function _processRememberMe()
    {
        $cookie_params = session_get_cookie_params();
        $cookie_params['lifetime'] = self::$_remember_me_seconds;
        
        $session_name = session_name();
        
        if (!isset($_COOKIE[$session_name])) {
            // somehow the rememberme cookie is here but not the session cookie
            return;
        }
        
        $session_id = $_COOKIE[$session_name];

        // this will send 2 cookies for some reason.. possible php bug
        setcookie('REMEMBERME', false, time()-(60*60*24*2), $cookie_params['path'], $cookie_params['domain'], $cookie_params['secure']);
        session_set_cookie_params($cookie_params['lifetime'], $cookie_params['path'], $cookie_params['domain'], $cookie_params['secure']);
        
        // force new cookie to be set
        session_id($session_id);
        
        return;
    }
    
    
    /**
     * _processValidator() - internal function that is called in the existence of VALID metadata
     * 
     * @return void
     */
    protected static function _processValidators()
    {
        foreach ($_SESSION['__ZF']['VALID'] as $validator_name => $valid_data) {
            Zend::loadClass($validator_name);
            $validator = new $validator_name;
            if ($validator->validate() === false) {
                throw new Zend_Session_Exception("This session is not valid according to {$validator_name}.");
            }
        }
        
        return;
    }
    
    
    /**
     * INSTANACE METHODS
     */
    
    
    /**
     * Constructor 
     *
     * @access private *not really but we would like it to be.
     * @param string $namespace
     * @return void
     */
    public function __construct()
    {
        if (self::$_strict === true && self::$_session_started === false) {
            throw new Zend_Session_Exception('You must start the session with Zend_Session_Core::start() when session options are set to strict.');
        }
        
        if (self::$_instance !== null || self::$_singleton === false) {
            throw new Zend_Session_Exception('Zend_Session_Core should be initialized through Zend_Session_Core::getInstance() only.');
        }
        
        if (self::$_session_started === false) {
            self::start();
        }
        
        return;
    }
    
    
    /**
     * Clone overriding - make sure that a developer cannot clone the core instance
     *
     * @throws Zend_Session_Exception
     */
    public function __clone()
    {
        throw new Zend_Session_Exception('Cloning the Zend_Session_Core object is not allowed as this is implemented as a singleton pattern.');
    }
    
    
    /**
     * _startNamespace() - while this method is public, its really only intended use is
     * by the constructor of Zend_Session object.  This method initializes the session namespace.
     *
     * @param string $namespace
     */
    public function _startNamespace($namespace)
    {
        self::_processStartupMetadataNamespace($namespace);
    }
    
    
    /**
     * namespaceIsset() - check to see if a namespace or a variable within a namespace is set
     *
     * @param string $namespace
     * @param string $name
     * @return bool
     */
    public function namespaceIsset($namespace, $name = null)
    {
        $return_value = null;
        
        if (self::$_log_level >= 2) {
            Zend_Log::log('Session isset called for namespace (' . $namespace . ') and variable (' . $name . ')', Zend_Log::LEVEL_DEBUG);
        }
        
        if ($name === null) {
            return ( isset($_SESSION[$namespace]) || isset(self::$_expiring_data[$namespace]) );
        } else {
            return ( isset($_SESSION[$namespace][$name]) || isset(self::$_expiring_data[$namespace][$name]) );
        }
    }
    
    
    /**
     * namespaceUnset() - unset a namespace or a variable within a namespace
     *
     * @param string $namespace
     * @param string $name
     * @return void
     */
    public function namespaceUnset($namespace, $name = null) 
    {
        if (self::$_log_level >= 2) {
            Zend_Log::log('Session unset called for namespace (' . $namespace . ') and variable (' . $name . ')', Zend_Log::LEVEL_DEBUG);
        }
        
        // check to see if the api wanted to remove a var from a namespace or a namespace
        if ($name === null) {
            unset($_SESSION[$namespace]);
            unset(self::$_expiring_data[$namespace]);
        } else {
            unset($_SESSION[$namespace][$name]);
            unset(self::$_expiring_data[$namespace]);
        }
            
        // if we remove the last value, remove namespace.
        if (empty($_SESSION[$namespace])) {
            unset($_SESSION[$namespace]);
        }
            
        return;
    }
    
    
    /**
     * namespaceSet() - set a variable within a namespace.
     *
     * @param string $namespace
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function namespaceSet($namespace, $name, $value) 
    {
        if (self::$_log_level >= 2) {
            Zend_Log::log('Session set called for namespace (' . $namespace . '), variable (' . $name . ') and value (' . $value . ')', Zend_Log::LEVEL_DEBUG);
        }
        
        $_SESSION[$namespace][$name] = $value;
        return;
    }
    
    
    /**
     * NamespaceGet() - get a variable from a namespace.
     *
     * @param string $namespace
     * @param string $name
     * @return mixed
     */
    public function namespaceGet($namespace, $name = null)
    {
        if (self::$_log_level >= 2) {
            Zend_Log::log('Session get called for namespace (' . $namespace . ') and variable (' . $name . ')', Zend_Log::LEVEL_DEBUG);
        }
        
        if ($name === null) {
            
            if (isset($_SESSION[$namespace])) {
                return $_SESSION[$namespace];
            } elseif (isset(self::$_expiring_data[$namespace])) {
                return self::$_expiring_data[$namespace];
            } else {
                return null;
            }
            
        } else {
            
            if (isset($_SESSION[$namespace][$name])) {
                return $_SESSION[$namespace][$name];
            } elseif (isset(self::$_expiring_data[$namespace][$name])) {
                return self::$_expiring_data[$namespace][$name];
            } else {
                return null;
            }
            
        }
    }

    
    /**
     * NamespaceSetExpirationSeconds() - exprire a namespace, or data within after a specified number
     * of seconds.
     *
     * @param string $namespace
     * @param int $seconds
     * @param mixed $variables
     * @return void
     */
    public function namespaceSetExpirationSeconds($namespace, $seconds, $variables = null)
    {
        if ($seconds <= 0) {
            throw new Zend_Session_Exception("Seconds must be positive.");
        }
        
        if ($variables === null) {
            
            // apply expiration to entire namespace
            $_SESSION['__ZF'][$namespace]['ENT'] = time() + $seconds;
            
        } else {
            
            if (is_string($variables)) {
                $variables = array($variables);
            }

            foreach ($variables as $variable) {
                if ($variable != "") {
                    $_SESSION['__ZF'][$namespace]['ENVT'][$variable] = time() + $seconds;
                }
            }
                     
            return;
        }
        
        return;
    }
    
    
    /**
     * NamespaceSetExpirationHops() - 
     *
     * @param string $namespace
     * @param int $hops
     * @param mixed $variables
     * @param bool $hop_count_on_usage_only
     * @return void
     */
    public function namespaceSetExpirationHops($namespace, $hops, $variables = null, $hop_count_on_usage_only = false)
    {
        if ($hops <= 0) {
            throw new Zend_Session_Exception("Hops must be positive number.");
        }
        
        if ($variables === null) {
            
            // apply expiration to entire namespace
            if ($hop_count_on_usage_only === false) {
                $_SESSION['__ZF'][$namespace]['ENGH'] = $hops;
            } else {
                $_SESSION['__ZF'][$namespace]['ENNH'] = $hops;
            }
                
        } else {
            
            if (is_string($variables)) {
                $variables = array($variables);
            }
                
            foreach ($variables as $variable) {
                if ($variable != "") {
                    if ($hop_count_on_usage_only === false) {
                        $_SESSION['__ZF'][$namespace]['ENVGH'][$variable] = $hops;
                    } else {
                        $_SESSION['__ZF'][$namespace]['ENVNH'][$variable] = $hops;
                    }
                }
            }

            return;
        }
        
        return;
    }


}
