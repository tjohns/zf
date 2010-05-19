<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage\Pluggable;
use \Zend\Cache\Storage\Adaptable;
use \Zend\Cache\Storage;
use \Zend\Cache\InvalidArgumentException;
use \Zend\Cache\BadMethodCallException;
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
        return $this;
    }

    public function getOptions()
    {
        $storage = $this->getStorage();
        $options = $storage->getOptions();
        $options['storage'] = $storage;
        $options['adapter'] = $this->getAdapter();

        return $options;
    }

    public function getStorage()
    {
        return $this->_storage;
    }

    public function setStorage(Adaptable $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    public function getAdapter()
    {
        $storage = $this->getStorage();
        if ($storage instanceof Pluggable) {
            return $storage->getAdapter();
        } else {
            return $storage;
        }
    }

    public function setAdapter(Adaptable $adapter)
    {
        $storage = $this->getStorage();
        if ($storage instanceof Pluggable) {
            $storage->setAdapter($adapter);
        } else {
            $this->setStorage($adapter);
        }

        return $this;
    }

    public function getCapabilities()
    {
        return $this->getStorage()->getCapabilities();
    }

    public function set($value, $key = null, array $options = array())
    {
        return $this->getStorage()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        return $this->getStorage()->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array())
    {
        return $this->getStorage()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        return $this->getStorage()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array())
    {
        return $this->getStorage()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        return $this->getStorage()->replaceMulti($keyValuePairs, $options);
    }

    public function remove($key = null, array $options = array())
    {
        return $this->getStorage()->remove($key, $options);
    }

    public function removeMulti(array $keys, array $options = array())
    {
        return $this->getStorage()->removeMulti($keys, $options);
    }

    public function get($key = null, array $options = array())
    {
        return $this->getStorage()->get($key, $options);
    }

    public function getMulti(array $keys, array $options = array())
    {
        return $this->getStorage()->getMulti($keys, $options);
    }

    public function exists($key = null, array $options = array())
    {
        return $this->getStorage()->exists($key, $options);
    }

    public function existsMulti(array $keys, array $options = array())
    {
        return $this->getStorage()->existsMulti($keys, $options);
    }

    public function info($key = null, array $options = array())
    {
        return $this->getStorage()->info($key, $options);
    }

    public function infoMulti(array $keys, array $options = array())
    {
        return $this->getStorage()->infoMulti($keys, $options);
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        return $this->getStorage()->getDelayed($keys, $select, $options);
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        return $this->getStorage()->fetch($fetchStyle);
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        return $this->getStorage()->fetchAll($fetchStyle);
    }

    public function increment($value, $key = null, array $options = array())
    {
        return $this->getStorage()->increment($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        return $this->getStorage()->incrementMulti($keyValuePairs, $options);
    }

    public function decrement($value, $key = null, array $options = array())
    {
        return $this->getStorage()->decrement($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        return $this->getStorage()->decrementMulti($keyValuePairs, $options);
    }

    public function touch($key = null, array $options = array())
    {
        return $this->getStorage()->touch($key, $options);
    }

    public function touchMulti(array $keys, array $options = array())
    {
        return $this->getStorage()->touchMulti($keys, $options);
    }

    public function find($match = Storage::MATCH_ACTIVE, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        return $this->getStorage()->find($match, $select, $options);
    }

    public function clear($match = Storage::MATCH_EXPIRED, array $options = array())
    {
        return $this->getStorage()->clear($match, $options);
    }

    public function status(array $options)
    {
        return $this->getStorage()->status($options);
    }

    public function optimize(array $options = array())
    {
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
