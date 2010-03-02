<?php

namespace zend\cache\plugin;
use \zend\Cache as Cache;
use \zend\Options as Options;
use \zend\cache\adapter\AdapterInterface as AdapterInterface;
use \zend\cache\InvalidArgumentException as InvalidArgumentException;

abstract class PluginAbstract implements PluginInterface
{

    protected $_adapter;

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);

        if (!$this->_adapter) {
            throw new InvalidArgumentException('Missing option "adapter"');
        }
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
    }

    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function setAdapter(AdapterInterface $innerAdapter)
    {
        $this->_adapter = $innerAdapter;
    }

    public function getCapabilities() {
        return $this->getAdapter()->getCapabilities();
    }

    public function set($value, $key = null, array $options = array()) {
        return $this->getAdapter()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array()) {
        return $this->getAdapter()->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array()) {
        return $this->getAdapter()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array()) {
        return $this->getAdapter()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array()) {
        return $this->getAdapter()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array()) {
        return $this->getAdapter()->replaceMulti($keyValuePairs, $options);
    }

    public function remove($key = null, array $options = array()) {
        return $this->getAdapter()->remove($key, $options);
    }

    public function removeMulti(array $keys, array $options = array()) {
        return $this->getAdapter()->removeMulti($keys, $options);
    }

    public function get($key = null, array $options = array()) {
        return $this->getAdapter()->get($key, $options);
    }

    public function getMulti(array $keys, array $options = array()) {
        return $this->getAdapter()->getMulti($keys, $options);
    }

    public function exists($key = null, array $options = array()) {
        return $this->getAdapter()->exists($key, $options);
    }

    public function existsMulti(array $keys, array $options = array()) {
        return $this->getAdapter()->existsMulti($keys, $options);
    }

    public function info($key = null, array $options = array()) {
        return $this->getAdapter()->info($key, $options);
    }

    public function infoMulti(array $keys, array $options = array()) {
        return $this->getAdapter()->infoMulti($keys, $options);
    }

    public function getDelayed(array $keys, array $options = array()) {
        return $this->getAdapter()->getDelayed($keys, $options);
    }

    public function fetch() {
        return $this->getAdapter()->fetch();
    }

    public function fetchAll() {
        return $this->getAdapter()->fetchAll();
    }

    public function increment($value, $key = null, array $options = array()) {
        return $this->getAdapter()->increment($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array()) {
        return $this->getAdapter()->incrementMulti($keyValuePairs, $options);
    }

    public function decrement($value, $key = null, array $options = array()) {
        return $this->getAdapter()->decrement($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array()) {
        return $this->getAdapter()->decrementMulti($keyValuePairs, $options);
    }

    public function find($match = Cache::MATCH_ACTIVE, array $options = array()) {
        return $this->getAdapter()->find($match, $options);
    }

    public function clear($match = Cache::MATCH_EXPIRED, array $options = array()) {
        return $this->getAdapter()->clear($match, $options);
    }

    public function status(array $options) {
        return $this->getAdapter()->status($options);
    }

    public function optimize(array $options = array()) {
        return $this->getAdapter()->optimize($options);
    }

    public function lastKey()
    {
        return $this->getAdapter()->lastKey();
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->getAdapter(), $method), $args);
    }

}
