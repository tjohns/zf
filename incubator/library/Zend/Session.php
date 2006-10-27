<?php

/**
 * Zend_Session_Core
 */
require_once 'Zend/Session/Core.php';

/**
 * Zend_Session_Exception
 */
require_once 'Zend/Session/Exception.php';


/**
 * Zend_Session
 *
 */
class Zend_Session
{
    
    /**
     * Session_Core instance
     *
     * @var Zend_Session_Core
     */
    protected $_session_core = null;

    /**
     * Namespace - which namespace this instance of zend-session is saving-to/getting-from
     *
     * @var string
     */
    protected $_namespace = "Default";

    /**
     * Namespace locking mechanism
     *
     * @var array
     */
    protected static $_namespace_locks = array();
    

    /**
     * __construct() - This will create an instance that saves to/gets from an 
     * instantiated core.  An optional namespace allows for saving/getting
     * to isolated sections of the session.
     *
     * @param string $namespace
     */
    public function __construct($namespace = 'Default')
    {
        if (!is_string($namespace)) {
            throw new Zend_Session_Exception("Namespace must be a string.");
        }
            
        if ($namespace[0] == "_") {
            throw new Zend_Session_Exception("Namespace must not start with an underscore.");
        }
        
        $this->_namespace = $namespace;
        $this->_session_core = Zend_Session_Core::getInstance();
        $this->_session_core->_startNamespace($namespace);
    }


    /**
     * SetExpirationSeconds() - expire the namespace, or specific variables after a specified
     * number of seconds
     *
     * @param int $seconds
     * @param mixed $variables
     * @return void
     */
    public function setExpirationSeconds($seconds, $variables = null)
    {
        $this->_session_core->namespaceSetExpirationSeconds($this->_namespace, $seconds, $variables);
        return;
    }
    
    
    /**
     * SetExpirationHops() - expire the namespace, or specific variables after a specified 
     * number of page hops
     *
     * @param int $hops
     * @param mixed $variables
     * @param boolean $hop_count_on_usage_only
     * @return void
     */
    public function setExpirationHops($hops, $variables = null, $hop_count_on_usage_only = false)
    {
        $this->_session_core->namespaceSetExpirationHops($this->_namespace, $hops, $variables, $hop_count_on_usage_only);
        return;
    }
    
    
    /**
     * Lock() - ability to mark a session/namespace as readonly
     *
     * @return void
     */
    public function lock($locked = true)
    {
        self::$_namespace_locks[$this->_namespace] = $locked;
        return;
    }

    
    /**
     * __get() - method to get a variable in this objects current namespace
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_session_core->namespaceGet($this->_namespace, $name);
    }
    
    
    /**
     * __set() - method to set a variable/value in this objects namespace
     *
     * @param string $name
     * @param mixed $value
     * @return true
     */
    public function __set($name, $value) 
    {
        if (isset(self::$_namespace_locks[$this->_namespace]) && self::$_namespace_locks[$this->_namespace] === true) {
            throw new Zend_Session_Exception("This session/namespace has been marked as read-only.");
        }
        
        return $this->_session_core->namespaceSet($this->_namespace, $name, $value);
    }
    
    
    /**
     * __isset() - determine if a variable in this objects namespace is set
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) 
    {
        return $this->_session_core->namespaceIsset($this->_namespace, $name);
    }
    
    
    /**
     * __unset() - unset a variable in this objects namespace.
     *
     * @param string $name
     * @return true
     */
    public function __unset($name)
    {
        return $this->_session_core->namespaceUnset($this->_namespace, $name);
    }
    
}
