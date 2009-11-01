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
 * @package    Zend_Entity
 * @subpackage Collection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Array Abstraction
 *
 * @uses       Zend_Entity_Collection_Interface
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Collection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Collection_Array implements Zend_Entity_Collection_Interface
{
    /**
     * @var array
     */
    protected $_elements = array();

    /**n
     * @var array
     */
    protected $_added = array();

    /**
     * @var array
     */
    protected $_removed = array();

    /**
     * @param array $elements
     */
    public function __construct(array $elements=array())
    {
        $this->_elements = $elements;
    }

    public function key()
    {
        return key($this->_elements);
    }

    public function next()
    {
        return next($this->_elements);
    }

    public function current()
    {
        return current($this->_elements);
    }

    public function valid()
    {
        return $this->current()!==false;
    }

    public function rewind()
    {
        return reset($this->_elements);
    }

    public function offsetGet($index)
    {
        if(isset($this->_elements[$index])) {
            return $this->_elements[$index];
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Unknown key '".$index."'."
            );
        }
    }

    public function offsetSet($index, $value)
    {
        if(is_string($index) && strlen($index) > 0) {
            if(!is_string($value) && !is_int($value) && !is_float($value) && !is_bool($value)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "Invalid datatype '".gettype($value)."' given for ".
                    "index '".$index."', only string, int, float or boolean allowed."
                );
            }

            if(isset($this->_elements[$index])) {
                $this->_removed[$index] = $index;
            }
            $this->_added[$index] = $value;
            $this->_elements[$index] = $value;
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Invalid hash-map key. Has to be string of a size ".
                "larger zero, '".gettype($index)."' was given."
            );
        }
    }

    public function offsetExists($index)
    {
        return isset($this->_elements[$index]);
    }

    public function offsetUnset($index)
    {
        $this->_removed[$index] = $index;
        unset($this->_elements[$index]);
    }

    public function count()
    {
        return count($this->_elements);
    }

    public function __ze_getRemoved()
    {
        return $this->_removed;
    }

    public function __ze_getAdded()
    {
        return $this->_added;
    }

    public function __ze_wasLoadedFromDatabase()
    {
        return true;
    }

    public function clear()
    {
        foreach($this AS $k => $v) {
            $this->offsetUnset($k);
        }
    }
}