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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Config_Exception
 */
require_once 'Zend/Config/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Config implements Countable, Iterator
{
    protected $_allowModifications;
    protected $_iterationPointerValid;
    protected $_data;

    /**
     * Zend_Config provides a property based interface to
     * an array. The data are read only unless $allowModifications
     * is set to true on construction.
     *
     * Zend_Config also implements Countable and Iterator to
     * facilitate easy access to the data.
     *
     * @param array $array
     * @param boolean $allowModifications
     * @throws Zend_Config_Exception
     */
    public function __construct($array, $allowModifications = false)
    {
        $this->_allowModifications = $allowModifications;
        foreach ($array as $key => $value) {
            if ($this->_isValidKeyName($key)) {
                if (is_array($value)) {
                    $this->_data[$key] = new Zend_Config($value, $allowModifications);
                } else {
                    $this->_data[$key] = $value;
                }
            } else {
                throw new Zend_Config_Exception("Invalid key: '$key'");
            }
        }
    }

    /**
     * Ensure that the key is a valid PHP property name
     *
     * @param string $key
     * @return boolean
     */
    protected function _isValidKeyName($key)
    {
        return (bool) preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $key);
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $result = null;
        if (isset($this->_data[$name])) {
            $result = $this->_data[$name];
        }
        return $result;
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param string $name
     * @param mixed $value
     * @throws Zend_Config_Exception
     */
    public function __set($name, $value)
    {
        if ($this->_allowModifications) {
            if (is_array($value)) {
                $this->_data[$name] = new Zend_Config($value, true);
            } else {
                $this->_data[$name] = $value;
            }
        } else {
            throw new Zend_Config_Exception('Zend_Config is read only');
        }
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function asArray()
    {
        $array = array();
        foreach ($this->_data as $key=>$value) {
            if (is_object($value)) {
                $array[$key] = $value->asArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
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
            $this->_iterationPointerValid = false;
        } else {
            $this->_iterationPointerValid = true;
        }
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function rewind ()
    {
        reset($this->_data);
        $this->_iterationPointerValid = true;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid ()
    {
        return $this->_iterationPointerValid;
    }

}
