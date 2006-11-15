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
 * @package    Zend_Session
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 * @since      Preview Release 0.2
 */

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
 * @category Zend
 * @package Zend_Session
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Session implements IteratorAggregate
{
    
    /**
     * Session_Core instance
     *
     * @var Zend_Session_Core
     */
    protected $_sessionCore = null;

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
    protected static $_namespaceLocks = array();
    

    /**
     * __construct() - This will create an instance that saves to/gets from an 
     * instantiated core.  An optional namespace allows for saving/getting
     * to isolated sections of the session.
     *
     * @param string $namespace
     */
    public function __construct($namespace = 'Default')
    {
        if (!is_string($namespace) || $namespace !== '') {
            throw new Zend_Session_Exception("Namespace must be a non-empty string.");
        }
            
        if ($namespace[0] == "_") {
            throw new Zend_Session_Exception("Namespace must not start with an underscore.");
        }
        
        $this->_namespace = $namespace;
        $this->_sessionCore = Zend_Session_Core::getInstance();
        $this->_sessionCore->_startNamespace($namespace);
    }
    
    
    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject
     */
    public function getIterator()
    {
        $name_values = $this->_sessionCore->namespaceGet($this->_namespace);
        
        return new ArrayObject($name_values);
    }


    /**
     * setExpirationSeconds() - expire the namespace, or specific variables after a specified
     * number of seconds
     *
     * @param int $seconds
     * @param mixed $variables
     * @return void
     */
    public function setExpirationSeconds($seconds, $variables = null)
    {
        $this->_sessionCore->namespaceSetExpirationSeconds($this->_namespace, $seconds, $variables);
        return;
    }
    
    
    /**
     * setExpirationHops() - expire the namespace, or specific variables after a specified 
     * number of page hops
     *
     * @param int $hops
     * @param mixed $variables
     * @param boolean $hop_count_on_usage_only
     * @return void
     */
    public function setExpirationHops($hops, $variables = null, $hopCountOnUsageOnly = false)
    {
        $this->_sessionCore->namespaceSetExpirationHops($this->_namespace, $hops, $variables, $hopCountOnUsageOnly);
        return;
    }
    
    
    /**
     * lock() - mark a session/namespace as readonly
     *
     * @return void
     */
    public function lock()
    {
        self::$_namespaceLocks[$this->_namespace] = null;
        return;
    }


    /**
     * unlock() - unmark a session/namespace to enable read & write
     *
     * @return void
     */
    public function unlock()
    {
        unset(self::$_namespaceLocks[$this->_namespace]);
        return;
    }

    
    /**
     * unsetAll() - unset all variables in this namespace
     *
     * @return void
     */
    public function unsetAll()
    {
        foreach ($this as $name => $value) {
            unset($this->{$name});
        }
        
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
        return $this->_sessionCore->namespaceGet($this->_namespace, $name);
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
        if (isset(self::$_namespaceLocks[$this->_namespace]) && self::$_namespaceLocks[$this->_namespace] === true) {
            throw new Zend_Session_Exception("This session/namespace has been marked as read-only.");
        }
        
        return $this->_sessionCore->namespaceSet($this->_namespace, $name, $value);
    }
    
    
    /**
     * __isset() - determine if a variable in this objects namespace is set
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) 
    {
        return $this->_sessionCore->namespaceIsset($this->_namespace, $name);
    }
    
    
    /**
     * __unset() - unset a variable in this objects namespace.
     *
     * @param string $name
     * @return true
     */
    public function __unset($name)
    {
        return $this->_sessionCore->namespaceUnset($this->_namespace, $name);
    }
  
}
