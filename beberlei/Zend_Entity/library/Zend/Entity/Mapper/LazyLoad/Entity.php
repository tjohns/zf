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

class Zend_Entity_Mapper_LazyLoad_Entity implements Zend_Entity_Interface
{
    protected $callback;
    protected $args;

    protected $object;

    public function __construct($callback, array $args)
    {
        if(!is_callable($callback)) {
            throw new Exception("Callback is not callble.");
        }
        $this->callback = $callback;
        $this->args     = $args;
    }

    protected function getObject()
    {
        if($this->object == null) {
            $this->object = call_user_func_array($this->callback, $this->args);
        }
        return $this->object;
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

    public function entityWasLoaded()
    {
        if($this->object == null) {
            return false;
        }
        return true;
    }
}