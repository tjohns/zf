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
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_Environment_Exception
 */
require_once 'Zend/Environment/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Environment_Container_Abstract implements Countable, Iterator
{
    /**
     * Container for class properties
     */
    protected $_data;

    /**
     * Pointer to provide for Iterator
     */
    protected $_ptr;

    /**
     * Magic method for retrieving properties.
     *
     * @param string $key
     * @throws Zend_Environment_Exception
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
        
        throw new Zend_Environment_Exception("Property '{$key}' does not exist");
    }

    /**
     * Magic method for setting properties.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return boolean
     */
    protected function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Convert the instance to an associative array 
     *
     * @return array
     */
	public function asArray()
	{
	    $array = array();

	    foreach ($this->_data as $key => $val) {
            if ($val instanceof Zend_Environment_Container_Interface) {
                $array[$key] = $val->asArray();
            } else {
                $array[$key] = $val;
            }
	    }

	    return $array;
	}

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function next()
    {
        if (next($this->_data) === false) {
            $this->_ptr = false;
        } else {
            $this->_ptr = true;
        }
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function rewind ()
    {
        reset($this->_data);
        $this->_ptr = true;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid ()
    {
        return $this->_ptr;
    }
}
