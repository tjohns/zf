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

class Zend_Entity_LazyLoad_Entity implements Zend_Entity_Interface
{
    /**
     * @var array
     */
    protected $_callback;

    /**
     * @var array
     */
    protected $_callbackArguments;

    /**
     * @var Zend_Entity_Interface
     */
    protected $_object;

    /**
     * @var int
     */
    protected $_lazyLoadEntityId;

    /**
     * @param array|string $callback
     * @param array $args
     */
    public function __construct($callback, array $args=array())
    {
        if(!is_callable($callback)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("Invalid callback given.");
        }
        $this->_callback = $callback;
        $this->_callbackArguments     = $args;
        $this->_lazyLoadEntityId = $args[1];
    }

    /**
     * Retrieve the original object from the database if not already done so.
     * 
     * @return Zend_Entity_Interface
     */
    public function getObject()
    {
        if($this->_object == null) {
            $this->_object = call_user_func_array($this->_callback, $this->_callbackArguments);
        }
        return $this->_object;
    }

    public function getState()
    {
        return $this->getObject()->getState();
    }

    public function setState(array $state)
    {
        $this->getObject()->setState($state);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->getObject(), $method), $args);
    }

    public function __get($name)
    {
        return $this->getObject()->$name;
    }

    public function __set($name, $value)
    {
        $this->getObject()->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->getObject()->$name);
    }

    public function __unset($name)
    {
        unset($this->getObject()->$name);
    }

    /**
     * @return boolean
     */
    public function entityWasLoaded()
    {
        if($this->_object == null) {
            return false;
        }
        return true;
    }

    /**
     * @return int
     */
    public function getLazyLoadEntityId()
    {
        return $this->_lazyLoadEntityId;
    }
}