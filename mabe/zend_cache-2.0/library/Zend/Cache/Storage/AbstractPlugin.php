<?php

namespace Zend\Cache\Storage;
use \Zend\Cache;
use \Zend\Cache\InvalidArgumentException;
use \Zend\Ccache\BadMethodCallException;
use \Zend\Options;

abstract class AbstractPlugin implements Pluggable
{

    protected $_storage;

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);

        if (!$this->_storage) {
            throw new InvalidArgumentException("Missing option 'storage'");
        }
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
    }

    public function getOptions()
    {
        $storage = $this->getStorage();
        $options = $storage->getOptions();
        $options['storage']     = $storage;
        $options['mainStorage'] = $this->getMainStorage();

        return $options;
    }

    public function getStorage()
    {
        return $this->_storage;
    }

    public function setStorage($storage)
    {
        if (is_string($storage)) {
            $storage = Storage::adapterFactory($storage);
        } elseif ( !($storage instanceof Storable) ) {
            throw new InvalidArgumentException(
                'The storage must implement Zend\Cache\Storage\Storable '
              . 'or must be the name of the storage adapter'
            );
        }

        $this->_storage = $storage;
        return $this;
    }

    public function getMainStorage()
    {
        $storage = $this->getStorage();
        if ($storage instanceof Pluggable) {
            return $storage->getMainStorage();;
        } else {
            return $storage;
        }
    }

    public function setMainStorage($storage)
    {
        if (is_string($storage)) {
            $storage = Storage::adapterFactory($storage);
        } elseif ( !($storage instanceof Storable) ) {
            throw new InvalidArgumentException(
                'The main storage must implement Zend\Cache\Storage\Storable '
              . 'or must be the name of the storage adapter'
            );
        }

        $innerStorage = $this->getStorage();
        if ($innerStorage instanceof Pluggable) {
            $innerStorage->setMainStorage($storage);
        } else {
            $this->setStorage($storage);
        }

        return $this;
    }

    public function getCapabilities() {
        return $this->getStorage()->getCapabilities();
    }

    public function set($value, $key = null, array $options = array()) {
        return $this->getStorage()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array()) {
        return $this->getStorage()->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array()) {
        return $this->getStorage()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array()) {
        return $this->getStorage()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array()) {
        return $this->getStorage()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array()) {
        return $this->getStorage()->replaceMulti($keyValuePairs, $options);
    }

    public function remove($key = null, array $options = array()) {
        return $this->getStorage()->remove($key, $options);
    }

    public function removeMulti(array $keys, array $options = array()) {
        return $this->getStorage()->removeMulti($keys, $options);
    }

    public function get($key = null, array $options = array()) {
        return $this->getStorage()->get($key, $options);
    }

    public function getMulti(array $keys, array $options = array()) {
        return $this->getStorage()->getMulti($keys, $options);
    }

    public function exists($key = null, array $options = array()) {
        return $this->getStorage()->exists($key, $options);
    }

    public function existsMulti(array $keys, array $options = array()) {
        return $this->getStorage()->existsMulti($keys, $options);
    }

    public function info($key = null, array $options = array()) {
        return $this->getStorage()->info($key, $options);
    }

    public function infoMulti(array $keys, array $options = array()) {
        return $this->getStorage()->infoMulti($keys, $options);
    }

    public function getDelayed(array $keys, array $options = array()) {
        return $this->getStorage()->getDelayed($keys, $options);
    }

    public function fetch() {
        return $this->getStorage()->fetch();
    }

    public function fetchAll() {
        return $this->getStorage()->fetchAll();
    }

    public function increment($value, $key = null, array $options = array()) {
        return $this->getStorage()->increment($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array()) {
        return $this->getStorage()->incrementMulti($keyValuePairs, $options);
    }

    public function decrement($value, $key = null, array $options = array()) {
        return $this->getStorage()->decrement($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array()) {
        return $this->getStorage()->decrementMulti($keyValuePairs, $options);
    }

    public function find($match = Storage::MATCH_ACTIVE, array $options = array()) {
        return $this->getStorage()->find($match, $options);
    }

    public function clear($match = Storage::MATCH_EXPIRED, array $options = array()) {
        return $this->getStorage()->clear($match, $options);
    }

    public function status(array $options) {
        return $this->getStorage()->status($options);
    }

    public function optimize(array $options = array()) {
        return $this->getStorage()->optimize($options);
    }

    public function lastKey()
    {
        return $this->getStorage()->lastKey();
    }

    public function __call($method, array $args)
    {
        $storage = $this->getStorage();
        if (!method_exists($storage, $method) && !method_exists($storage, '__call')) {
            throw new BadMethodCallException("Unknown method '{$method}'");
        }

        return call_user_func_array(array($storage, $method), $args);
    }

}
