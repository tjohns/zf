<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

/**
 * Collection that is used for lazy loading purposes of relations.
 *
 * Regarding the adding of new entries this collection attempts NOT to load the underlying
 * data if not necessary and allow for adding of elements without triggering the lazy load.
 *
 */
class Zend_Entity_LazyLoad_Collection implements Zend_Entity_Collection_Interface
{
    /**
     * Inner Iterator
     *
     * @var Zend_Entity_Collection
     */
    protected $_collection = null;

    /**
     * PHP Callback for LazyLoading.
     *
     * @var string|array
     */
    protected $_callback;

    /**
     * Callback arguments
     *
     * @var array
     */
    protected $_callbackArguments;

    /**
     * Added entities.
     *
     * @var array
     */
    protected $_added;

    /**
     * Construct Lazy Load collection by giving a php callback and its arguments.
     * 
     * @param callback $callback
     * @param array    $arguments
     */
    public function __construct($callback, array $arguments)
    {
        if(!is_callable($callback)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Invalid callback given for construcing lazy load collection."
            );
        }
        $this->_callback          = $callback;
        $this->_callbackArguments = $arguments;
    }

    /**
     * Triggers lazy load of the collection and returns it.
     *
     * @return Zend_Entity_Collection_Interface
     */
    protected function getInnerCollection()
    {
        if($this->_collection == null) {
            $collection = call_user_func_array($this->_callback, $this->_callbackArguments);
            if($collection instanceof Zend_Entity_Collection_Interface) {
                $this->_collection = $collection;
            } else if(is_array($collection)) {
                $this->_collection = new Zend_Entity_Collection($collection);
            } else {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "Inner collection has to be of type Zend_Entity_Collection."
                );
            }
            unset($this->_callback);
            unset($this->_callbackArguments);
        }
        return $this->_collection;
    }

    public function __ze_getRemoved()
    {
        return $this->getInnerCollection()->__ze_getRemoved();
    }

    public function __ze_getAdded()
    {
        return $this->getInnerCollection()->__ze_getAdded();
    }

    public function current()
    {
        return $this->getInnerCollection()->current();
    }

    public function valid()
    {
        return $this->getInnerCollection()->valid();
    }

    public function next()
    {
        return $this->getInnerCollection()->next();
    }

    public function key()
    {
        return $this->getInnerCollection()->key();
    }

    public function rewind()
    {
        return $this->getInnerCollection()->rewind();
    }

    public function count()
    {
        return $this->getInnerCollection()->count();
    }

    public function offsetExists($offset)
    {
        return $this->getInnerCollection()->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getInnerCollection()->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->getInnerCollection()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->getInnerCollection()->offsetUnset($offset);
    }

    public function __ze_wasLoadedFromDatabase()
    {
        if($this->_collection === null) {
            return false;
        }
        return $this->_collection->__ze_wasLoadedFromDatabase();
    }

    public function __sleep()
    {
        if($this->_collection === null) {
            throw new Zend_Entity_Exception("Cannot serialize non-loaded collection.");
        }
    }
}