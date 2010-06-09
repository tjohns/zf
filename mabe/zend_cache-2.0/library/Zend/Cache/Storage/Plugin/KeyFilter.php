<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Filter\Filter as FilterInterface;

// Use this KeyFilter plugin to:
//  - prefix/suffix your cache keys
//  - encode/decode your cache keys (e.g. make keys valid to store on Filesystem)

class KeyFilter extends AbstractPlugin
{

    /**
     * The key write filter
     *
     * @var Zend\Filter\Filter
     */
    protected $_keyWriteFilter = null;

    /**
     * The key read filter
     *
     * @var Zend\Filter\Filter
     */
    protected $_keyReadFilter = null;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['keyWriteFilter'] = $this->getKeyWriteFilter();
        $options['keyReadFilter']  = $this->getReadFilter();
        return $options;
    }

    /**
     * Get the write filter
     *
     * @return Zend\Filter\Filter
     */
    public function getKeyWriteFilter()
    {
        if ($this->_keyWriteFilter === null) {
            // TODO: empty filter
            $this->_keyWriteFilter = new \Zend\Filter\EmptyFilter();;
        }

        return $this->_keyWriteFilter;
    }

    /**
     * Get the write filter
     *
     * @param Zend\Filter\Filter
     * @return Zend\Cache\Storage\Plugin\Filter
     */
    public function setKeyWriteFilter(FilterInterface $filter)
    {
        $this->_keyWriteFilter = $filter;
        return $this;
    }

    /**
     * Get the read filter
     *
     * @return Zend\Filter\Filter
     */
    public function getKeyReadFilter()
    {
        if ($this->_keyReadFilter === null) {
            // TODO: empty filter
            $this->_keyReadFilter = new \Zend\Filter\EmptyFilter();
        }

        return $this->_keyReadFilter;
    }

    /**
     * Get the read filter
     *
     * @param Zend\Filter\Filter
     * @return Zend\Cache\Storage\Plugin\Filter
     */
    public function setKeyReadFilter(FilterInterface $filter)
    {
        $this->_keyReadFilter = $filter;
        return $this;
    }

    public function set($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ $keyWriteFilter->filter($key) ] = $v;
        }

        return $this->getAdapter()->setMulti($normalizedKeyValuePairs);
    }

    public function add($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ $keyWriteFilter->filter($key) ] = $v;
        }

        return $this->getAdapter()->addMulti($normalizedKeyValuePairs);
    }

    public function replace($value, $key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ $keyWriteFilter->filter($key) ] = $v;
        }

        return $this->getAdapter()->replaceMulti($normalizedKeyValuePairs);
    }

    public function remove($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->remove($key, $options);
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        foreach ($keys as &$key) {
            $key = $keyWriteFilter->filter($key);
        }

        return $this->getAdapter()->removeMulti($keys);
    }

    public function get($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->get($key, $options);
    }

    public function getMulti(array $keys, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        foreach ($keys as &$key) {
            $key = $keyWriteFilter->filter($key);
        }

        $rs = $this->getAdapter()->getMulti($keys);

        $keyReadFilter = $this->getKeyReadFilter();
        $normalizedRs = array();
        foreach ($rs as $key => &$v) {
            $normalizedRs[ $keyReadFilter->filter($key) ] = $v;
        }

        return $normalizedRs;
    }

    public function exists($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->exists($key, $options);
    }

    public function existsMulti(array $keys, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        foreach ($keys as &$key) {
            $key = $keyWriteFilter->filter($key);
        }

        $rs = $this->getAdapter()->existsMulti($keys);

        $keyReadFilter = $this->getKeyReadFilter();
        foreach ($rs as &$key) {
            $key = $keyReadFilter->filter($key);
        }

        return $rs;
    }

    public function info($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->info($key, $options);
    }

    public function infoMulti(array $keys, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        foreach ($keys as &$key) {
            $key = $keyWriteFilter->filter($key);
        }

        $rs = $this->getAdapter()->infoMulti($keys, $options);

        $keyReadFilter = $this->getKeyReadFilter();
        $normalizedRs = array();
        foreach ($rs as $key => &$v) {
            $normalizedRs[ $keyReadFilter->filter($key) ] = $v;
        }

        return $normalizedRs;;
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        foreach ($keys as &$key) {
            $key = $keyWriteFilter->filter($key);
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
            $item[0] = $this->getKeyReadFilter->filter($item[0]);
        } elseif ( ($fetchStyle == Storage::FETCH_ASSOC || $fetchStyle == Storage::FETCH_BOTH)
                && isset($item['key']) ) {
            $item['key'] = $this->getKeyReadFilter->filter($item['key']);
        } elseif ( $fetchStyle == Storage::FETCH_OBJ
                && isset($item->key) ) {
            $item->key = $this->getKeyReadFilter->filter($item->key);
        }

        return $item;
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        $rs = $this->getAdapter()->fetchAll($fetchStyle);
        $keyReadFilter = $this->getKeyReadFilter();
        foreach ($rs as &$item) {
            if ( ($fetchStyle == Storage::FETCH_NUM || $fetchStyle == Storage::FETCH_BOTH)
              && isset($item[0]) ) {
                $item[0] = $keyReadFilter->filter($item[0]);
            } elseif ( ($fetchStyle == Storage::FETCH_ASSOC || $fetchStyle == Storage::FETCH_BOTH)
                    && isset($item['key']) ) {
                $item['key'] = $keyReadFilter->filter($item['key']);
            } elseif ( $fetchStyle == Storage::FETCH_OBJ
                    && isset($item->key) ) {
                $item->key = $keyReadFilter->filter($item->key);
            }
        }

        return $rs;
    }

    public function increment($value, $key = null, array $options = array())
    {
       if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->increment($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ $keyWriteFilter->filter($key) ] = $v;
        }

        return $this->getAdapter()->incrementMulti($normalizedKeyValuePairs);
    }

    public function decrement($value, $key = null, array $options = array())
    {
       if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->decrement($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => &$v) {
            $normalizedKeyValuePairs[ $keyWriteFilter->filter($key) ] = $v;
        }

        return $this->getAdapter()->decrementMulti($normalizedKeyValuePairs);
    }

    public function touch($key = null, array $options = array())
    {
        if ( (string)$key !== '') {
            $key = $this->getKeyWriteFilter()->filter($key);
        }

        return $this->getAdapter()->touch($key, $options);
    }

    public function touchMulti(array $keys, array $options = array())
    {
        $keyWriteFilter = $this->getKeyWriteFilter();
        foreach ($keys as &$key) {
            $key = $keyWriteFilter->filter($key);
        }

        return $this->getAdapter()->touchMulti($keys);
    }

    public function lastKey()
    {
        $key = $this->getAdapter()->lastKey();
        if ($key !== null) {
            $key = $this->getKeyReadFilter->filter($key);
        }
        return $key;
    }

}
