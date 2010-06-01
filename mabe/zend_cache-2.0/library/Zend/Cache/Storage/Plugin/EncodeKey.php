<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;
use \Zend\Cache\InvalidArgumentException;

class EncodeKey extends AbstractPlugin
{

    /**
     * Callback to encode item key
     *
     * @var callback
     */
    protected $_keyEncoder = 'base64_encode';

    /**
     * Callback to decode an encoded item key
     *
     * @var callback
     */
    protected $_keyDecoder = 'base64_decode';

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['keyEncoder'] = $this->getKeyEncoder();
        $options['keyDecoder'] = $this->getKeyDecoder();
        return $options;
    }

    public function setKeyEncoder($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("Invalid callback '{$callback}'");
        }

        $this->_keyEncoder = $callback;
        return $this;
    }

    public function getKeyEncoder()
    {
        return $this->_keyEncoder;
    }

    public function setKeyDecodeFunction($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("Invalid callback '{$callback}'");
        }

        $this->_keyDecoder = $callback;
        return $this;
    }

    public function getKeyDecoder()
    {
        return $this->_keyDecoder;
    }

    public function set($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($keyEncoder, $key) ] = $v;
        }

        return $this->getAdapter()->setMulti($normalizedKeyValuePairs);
    }

    public function add($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($keyEncoder, $key) ] = $v;
        }

        return $this->getAdapter()->addMulti($normalizedKeyValuePairs);
    }

    public function replace($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($keyEncoder, $key) ] = $v;
        }

        return $this->getAdapter()->replaceMulti($normalizedKeyValuePairs);
    }

    public function remove($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->remove($key, $options);
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        foreach ($keys as &$key) {
            $key = call_user_func($keyEncoder, $key);
        }

        return $this->getAdapter()->removeMulti($keys);
    }

    public function get($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->get($key, $options);
    }

    public function getMulti(array $keys, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        foreach ($keys as &$key) {
            $key = call_user_func($keyEncoder, $key);
        }

        $rs = $this->getAdapter()->getMulti($keys);

        $keyDecoder = $this->getKeyDecoder();
        $normalizedRs = array();
        foreach ($rs as $key => &$v) {
            $normalizedRs[ call_user_func($keyDecoder, $key) ] = $v;
        }

        return $normalizedRs;
    }

    public function exists($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->exists($key, $options);
    }

    public function existsMulti(array $keys, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        foreach ($keys as &$key) {
            $key = call_user_func($keyEncoder, $key);
        }

        $rs = $this->getAdapter()->existsMulti($keys);

        $keyDecoder = $this->getKeyDecoder();
        foreach ($rs as &$key) {
            $key = call_user_func($keyDecoder, $key);
        }

        return $rs;
    }

    public function info($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->info($key, $options);
    }

    public function infoMulti(array $keys, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        foreach ($keys as &$key) {
            $key = call_user_func($keyEncoder, $key);
        }

        $rs = $this->getAdapter()->infoMulti($keys, $options);

        $keyDecoder = $this->getKeyDecoder();
        $normalizedRs = array();
        foreach ($rs as $key => &$v) {
            $normalizedRs[ call_user_func($keyDecoder, $key) ] = $v;
        }

        return $normalizedRs;;
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        foreach ($keys as &$key) {
            $key = call_user_func($keyEncoder, $key);
        }

        return $this->getAdapter()->getDelayed($keys, $select, $options);
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        $item = $this->getAdapter()->fetch($fetchStyle);
        if (!$item) {
            return $item;
        }

        if ( ($fetchStyle == Storage::FETCH_NUM || $fetchStyle == Storage::FETCH_BOTH)
          && isset($item[0]) ) {
            $item[0] = call_user_func($this->getKeyDecoder(), $item[0]);
        } elseif ( ($fetchStyle == Storage::FETCH_ASSOC || $fetchStyle == Storage::FETCH_BOTH)
                && isset($item['key']) ) {
            $item['key'] = call_user_func($this->getKeyDecoder(), $item['key']);
        } elseif ( $fetchStyle == Storage::FETCH_OBJ
                && isset($item->key) ) {
            $item->key = call_user_func($this->getKeyDecoder(), $item->key);
        }

        return $item;
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        $rs = $this->getAdapter()->fetchAll($fetchStyle);
        $keyDecoder = $this->getKeyDecoder();
        foreach ($rs as &$item) {
            if ( ($fetchStyle == Storage::FETCH_NUM || $fetchStyle == Storage::FETCH_BOTH)
              && isset($item[0]) ) {
                $item[0] = call_user_func($keyDecoder, $item[0]);
            } elseif ( ($fetchStyle == Storage::FETCH_ASSOC || $fetchStyle == Storage::FETCH_BOTH)
                    && isset($item['key']) ) {
                $item['key'] = call_user_func($keyDecoder, $item['key']);
            } elseif ( $fetchStyle == Storage::FETCH_OBJ
                    && isset($item->key) ) {
                $item->key = call_user_func($keyDecoder, $item->key);
            }
        }

        return $rs;
    }

    public function increment($value, $key = null, array $options = array())
    {
       if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->increment($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($keyEncoder, $key) ] = $v;
        }

        return $this->getAdapter()->incrementMulti($normalizedKeyValuePairs);
    }

    public function decrement($value, $key = null, array $options = array())
    {
       if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->decrement($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ call_user_func($keyEncoder, $key) ] = $v;
        }

        return $this->getAdapter()->decrementMulti($normalizedKeyValuePairs);
    }

    public function touch($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = call_user_func($this->getKeyEncoder(), $key);
        }

        return $this->getAdapter()->touch($key, $options);
    }

    public function touchMulti(array $keys, array $options = array())
    {
        $keyEncoder = $this->getKeyEncoder();
        foreach ($keys as &$key) {
            $key = call_user_func($keyEncoder, $key);
        }

        return $this->getAdapter()->touchMulti($keys);
    }

    public function lastKey()
    {
        $key = $this->getAdapter()->lastKey();
        if ($key !== null) {
            $key = call_user_func($this->getKeyDecoder(), $key);
        }
        return $key;
    }

}
