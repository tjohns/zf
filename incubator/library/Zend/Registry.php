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
 * @package    Zend_Registry
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Registry_Exception
 */
require_once 'Zend/Registry/Exception.php';
require_once 'Zend/Registry/Interface.php';


/**
 * Registry class for avoiding globals and some singletons
 *
 * @category   Zend
 * @package    Zend_Registry
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Registry implements Zend_Registry_Interface
{
	/**
	 * Singleton Instance
	 */
	static $instance = NULL;
	
    /**
     * Object registry provides storage for shared objects
     * @var array
     */
    protected $_registry = array();

    /**
     * Constructor
     */
    public function __construct()
    {}

	/**
	 * Retrieves the singleton instance creating one if necessary
	 * 
	 * @return object
	 */
	static function getInstance()
	{
		if (!self::$instance) self::$instance = new Zend_Registry();
		return self::$instance;
	}
	
	/**
	 * Sets or clears the singleton instance
	 * 
	 * @param  object $registry The new registry instance
	 * @return void
	 */
	static function setInstance($instance)
	{
		self::$instance = $instance;
	}
	
	
    /**
     * Registers a shared object.
     *
     * @todo use SplObjectStorage if ZF minimum PHP requirement moves up to at least PHP 5.1.0
     *
     * @param   string      $name The name for the object.
     * @param   object      $obj  The object to register.
     * @throws  Zend_Registry_Exception
     * @return  void
     */
    public function set($name, $obj)
    {
        if (!is_string($name)) {
            throw new Zend_Registry_Exception('First argument $name must be a string.');
        }

        // don't register the same name twice
        if (array_key_exists($name, $this->_registry)) {
           throw new Zend_Registry_Exception("Object named '$name' already registered.  Did you mean to call registry()?");
        }

        // only objects may be stored in the registry
        if (!is_object($obj)) {
           throw new Zend_Registry_Exception("Only objects may be stored in the registry.");
        }
        
        // don't register the same object twice
        if (($key = array_search($obj, $this->_registry, TRUE)) !== FALSE) {
           	throw new Zend_Registry_Exception("Duplicate object handle already exists in the registry as \"$key\".");
        }
        $this->_registry[$name] = $obj;
    }


    /**
     * Retrieves a registered shared object, where $name is the
     * registered name of the object to retrieve.
     *
     * If the $name argument is NULL, an array will be returned where 
	 * the keys to the array are the names of the objects in the registry 
	 * and the values are the class names of those objects.
     *
     * @see     register()
     * @param   string      $name The name for the object.
     * @throws  Zend_Registry_Exception
     * @return  object      The registered object.
     */
    public function get($name=null)
    {
        if ($name === null) {
            $registry = array();
            foreach ($this->_registry as $name=>$obj) {
                $registry[$name] = get_class($obj);
            }
            return $registry;
        }

        if (!is_string($name)) {
            throw new Zend_Registry_Exception('First argument $name must be a string, or null to list registry.');
        }

        if (!array_key_exists($name, $this->_registry)) {
           throw new Zend_Registry_Exception("No object named \"$name\" is registered.");
        }

        return $this->_registry[$name];
    }

    
    /**
     * Returns TRUE if the $name is a named object in the
     * registry, or FALSE if $name was not found in the registry.
     *
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->_registry[$name]);
    }
}
