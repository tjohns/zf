<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;
use \Zend\Cache\InvalidArgumentException;

class EncodeKey extends AbstractPlugin
{

    /**
     * The encoding function
     *
     * @var callback
     */
    protected $_keyEncodeFunction = 'base64_encode';

    /**
     * The decoding function
     *
     * @var callback
     */
    protected $_keyDecodeFunction = 'base64_decode';

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['keyEncodeFunction'] = $this->getKeyEncodeFunction();
        $options['keyDecodeFunction'] = $this->getKeyDecodeFunction();
        return $options;
    }

    public function setKeyEncodeFunction($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("Invalid callback '{$callback}'");
        }

        $this->_keyEncodeFunction = $callback;
        return $this;
    }

    public function getKeyEncodeFunction()
    {
        return $this->_keyEncodeFunction;
    }

    public function setKeyDecodeFunction($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("Invalid callback '{$callback}'");
        }

        $this->_keyDecodeFunction = $callback;
        return $this;
    }

    public function getKeyDecodeFunction()
    {
        return $this->_keyDecodeFunction;
    }

    public function set($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($this->getKeyEncodeFunction(), $key) ] = $v;
        }

        return $this->getAdapter()->setMulti($normalizedKeyValuePairs);
    }

    public function add($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($this->getKeyEncodeFunction(), $key) ] = $v;
        }

        return $this->getAdapter()->addMulti($normalizedKeyValuePairs);
    }

    public function replace($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($this->getKeyEncodeFunction(), $key) ] = $v;
        }

        return $this->getAdapter()->replaceMulti($normalizedKeyValuePairs);
    }

    public function remove($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->remove($key, $options);
    }

    public function removeMulti(array $keys, array $options = array())
    {
        foreach ($keys as &$key) {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->removeMulti($keys);
    }

    public function get($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->get($key, $options);
    }

    public function getMulti(array $keys, array $options = array())
    {
        foreach ($keys as &$key) {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        $rs = $this->getAdapter()->getMulti($keys);

        $normalizedRs = array();
        foreach ($rs as $key => &$v) {
            $normalizedRs[ call_user_func($this->getKeyDecodeFunction(), $key) ] = $v;
        }

        return $normalizedRs;
    }

    public function exists($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->exists($key, $options);
    }

    public function existsMulti(array $keys, array $options = array())
    {
        foreach ($keys as &$key) {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        $rs = $this->getAdapter()->existsMulti($keys);

        foreach ($rs as &$key) {
            $key = call_user_func($this->getKeyDecodeFunction(), $key);
        }

        return $rs;
    }

    public function info($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->info($key, $options);
    }

    public function infoMulti(array $keys, array $options = array())
    {
        foreach ($keys as &$key) {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        $rs = $this->getAdapter()->infoMulti($keys, $options);

        $normalizedRs = array();
        foreach ($rs as $key => &$v) {
            $normalizedRs[ call_user_func($this->getKeyDecodeFunction(), $key) ] = $v;
        }

        return $normalizedRs;;
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        foreach ($keys as &$key) {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->getDelayed($keys, $select, $options);
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        $item = $this->getAdapter()->fetch($fetchStyle);
        if (!$item) {
            return $item;
        }

        if ( $fetchStyle == Storage::FETCH_NUM
          || $fetchStyle == Storage::FETCH_BOTH) {
            $item[0] = call_user_func($this->getKeyDecodeFunction(), $item[0]);
        } elseif ( $fetchStyle == Storage::FETCH_ASSOC
                || $fetchStyle == Storage::FETCH_BOTH) {
            $item['key'] = call_user_func($this->getKeyDecodeFunction(), $item['key']);
        } elseif ($fetchStyle == Storage::FETCH_OBJ) {
            $item->key = call_user_func($this->getKeyDecodeFunction(), $item->key);
        }

        return $item;
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        $rs = $this->getAdapter()->fetchAll($fetchStyle);
        foreach ($rs as &$item) {
            if ( $fetchStyle == Storage::FETCH_NUM
              || $fetchStyle == Storage::FETCH_BOTH) {
                $item[0] = call_user_func($this->getKeyDecodeFunction(), $item[0]);
            } elseif ( $fetchStyle == Storage::FETCH_ASSOC
              || $fetchStyle == Storage::FETCH_BOTH) {
                $item['key'] = call_user_func($this->getKeyDecodeFunction(), $item['key']);
            } elseif ($fetchStyle == Storage::FETCH_OBJ) {
                $item->key = call_user_func($this->getKeyDecodeFunction(), $item->key);
            }
        }

        return $rs; 
    }

    public function increment($value, $key = null, array $options = array())
    {
       if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->increment($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($this->getKeyEncodeFunction(), $key) ] = $v;
        }

        return $this->getAdapter()->incrementMulti($normalizedKeyValuePairs);
    }

    public function decrement($value, $key = null, array $options = array())
    {
       if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->decrement($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($this->getKeyEncodeFunction(), $key) ] = $v;
        }

        return $this->getAdapter()->decrementMulti($normalizedKeyValuePairs);
    }

    public function touch($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->touch($key, $options);
    }

    public function touchMulti(array $keys, array $options = array())
    {
        foreach ($keys as &$key) {
            $key = call_user_func($this->getKeyEncodeFunction(), $key);
        }

        return $this->getAdapter()->touchMulti($keys);
    }

    public function lastKey()
    {
        $key = $this->getAdapter()->lastKey();
        if ($key !== null) {
            $key = call_user_func($this->getKeyDecodeFunction(), $key);
        }
        return $key;
    }

}
