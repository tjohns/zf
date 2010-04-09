<?php

namespace Zend\Cache\Storage;
use \Zend\Options;
use \Zend\Cache\Storage;
use \Zend\Cache\RuntimeException;
use \Zend\Cache\InvalidArgumentException;

abstract class AbstractAdapter implements Storable
{

    /**
     * All supported datatypes of this storage adapter
     * - Overwrites by class
     *
     * @var array
     */
    protected $_capabilities = array();

    /**
     * TTL option
     *
     * @var int 0 means infinite or maximum of adapter
     */
    protected $_ttl = 0;

    /**
     * The last used key
     *
     * @var string|null
     */
    protected $_lastKey = null;

    /**
     * The fetchBuffer for getDelayed and find.
     *
     * @var array
     */
    protected $_fetchBuffer = array();

    protected $_selectKeys = array(
            0 => 'key',
            1 => 'value',
            2 => 'tags',
            3 => 'mtime',
            4 => 'atime',
            5 => 'ctime'
    );

    public function __construct($options = array())
    {
        Options::setConstructorOptions($this, $options);
    }

    public function getCapabilities()
    {
        return $this->_capabilities;
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
    }

    public function getOptions()
    {
        return array(
            'ttl' => $this->getTtl(),
        );
    }

    public function setTtl($ttl)
    {
        $this->_ttl = $this->_ttl($ttl);
    }

    public function getTtl()
    {
        return $this->_ttl;
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->set($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->add($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->replace($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $ret = true;
        foreach ($keys as $key) {
            $ret = $this->remove($key) && $ret;
        }
        return $ret;
    }

    public function getMulti(array $keys, array $options = array())
    {
        $ret = array();
        foreach ($keys as $key) {
            if ( ($value = $this->get($key, $options)) ) {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    public function existsMulti(array $keys, array $options = array())
    {
        $ret = array();
        foreach ($keys as $key) {
            if ($this->exists($key, $options)) {
                $ret[] = $key;
            }
        }
        return $ret;
    }

    public function infoMulti(array $keys, array $options = array())
    {
        $ret = array();
        foreach ($keys as $key) {
            if ( ($info = $this->info($key, $options)) ) {
                $ret[$key] = $info;
            }
        }
        return $ret;
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        if ($this->_fetchBuffer) {
            throw new RuntimeException('Statement already in use');
        }

        $callback = null;
        if (isset($options['callback'])) {
            $callback = $options['callback'];
            if (!is_callable($callback, false)) {
                throw new Zend_Cache_Exception('Invalid callback');
            }
        }

        $select = (int)$select;
        $rsGet  = $this->getMulti($keys, $options);
        $rsInfo = null;
        if ($select > Storage::SELECT_VALUE) {
            $rsInfo = $this->infoMulti($keys, $options);
        }

        foreach ($rsGet as $key => &$value) {
            $item = array();
            if (($select & Storage::SELECT_KEY) == Storage::SELECT_KEY) {
                $item[0] = &$key;
            }
            if (($select & Storage::SELECT_VALUE) == Storage::SELECT_VALUE) {
                $item[1] = &$value;
            }

            if ($rsInfo !== null) {
                if (!isset($rsInfo[$key])) {
                    // ignore item if key isn't available in info result
                    continue;
                }

                $info = &$rsInfo[$key];
                if (($select & Storage::SELECT_TAGS) == Storage::SELECT_TAGS) {
                    $item[2] = isset($info['tags']) ? $info['tags'] : null;
                }
                if (($select & Storage::SELECT_MTIME) == Storage::SELECT_MTIME) {
                    $item[3] = isset($info['mtime']) ? $info['mtime'] : null;
                }
                if (($select & Storage::SELECT_ATIME) == Storage::SELECT_ATIME) {
                    $item[4] = isset($info['atime']) ? $info['atime'] : null;
                }
                if (($select & Storage::SELECT_CTIME) == Storage::SELECT_CTIME) {
                    $item[5] = isset($info['ctime']) ? $info['ctime'] : null;
                }
            }

            if ($callback !== null) {
                $this->_formatFetchItem($item);
                $callback($item);
            } else {
                $this->_fetchBuffer[] = $item;
            }
        }

        return true;
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        $item = array_shift($this->_fetchBuffer);
        if ($item === null) {
            return false;
        }

        $this->_formatFetchItem($item, $fetchStyle);
        return $item;
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        $rs = array();
        while ( ($item = $this->fetch($fetchStyle)) !== false ) {
            $rs[] = $item;
        }
        return $rs;
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->increment($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->decrement($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function optimize(array $options = array())
    {
        return true;
    }

    public function lastKey()
    {
        return $this->_lastKey;
    }

    protected function _ttl($ttl)
    {
        $ttl = (int)$ttl;
        if ($ttl < 0) {
             throw new InvalidArgumentException('The ttl can\'t be negative');
        }
        return $ttl;
    }

    /**
     * Get a normalized key.
     * - If key is empty get the last used key.
     *
     * @param string|null $key
     * @return string
     */
    protected function _key($key)
    {
        if ( ($key = (string)$key) === '') {
            if ($this->_lastKey === null) {
                throw new InvalidArgumentException('Missing key');
            }
        } else {
            $this->_lastKey = $key;
        }

        return $this->_lastKey;
    }

    /**
     * Normalize tags array
     *
     * @param array $tags
     * @return array
     * @throws Zend\Cache\InvalidArgumentException On invalid tags array
     */
    protected function _tags($tags)
    {
        if (!is_array($tags)) {
            throw new InvalidArgumentException('Tags have to be an array');
        }

        foreach ($tags as &$tag) {
            $tag = (string)$tag;
            if ($tag === '') {
                throw new InvalidArgumentException('Empty tags are not allowed');
            }
        }

        return array_values(array_unique($tags));
    }

    /**
     * Format an item for fetch response.
     *
     * @param array $item The Item formated as Storage::FETCH_NUM
     * @param int $fetchStyle The fetch style to format to
     * @throes Zend\Cache\RuntimeException
     */
    protected function _formatFetchItem(array &$item, $fetchStyle)
    {
        switch ($fetchStyle) {
            case Storage::FETCH_NUM:
                // nothing to do
                break;

            case Storage::FETCH_ASSOC:
                $assoc = array();
                foreach ($item as $key => &$value) {
                    if (!isset($this->_selectKeys[$key])) {
                        throw new RuntimeException("Unknown key '{$key}' on given item");
                    }
                    $assoc[$this->_selectKeys[$key]] = &$value;
                }
                $item = $assoc;
                break;

            case Storage::FETCH_BOTH:
                $array = array();
                foreach ($item as $key => &$value) {
                    if (!isset($this->_selectKeys[$key])) {
                        throw new RuntimeException("Unknown key '{$key}' on given item");
                    }
                    $array[$key]                     = &$value;
                    $array[$this->_selectKeys[$key]] = &$value;
                }
                $item = $array;
                break;

            case Storage::FETCH_OBJ:
                $obj = new \stdClass;
                foreach ($item as $key => &$value) {
                    if (!isset($this->_selectKeys[$key])) {
                        throw new RuntimeException("Unknown key '{$key}' on given item");
                    }
                    $obj->{$this->_selectKeys[$key]} = &$value;
                }
                $item = $obj;
                break;

            default:
                throw new RuntimeException("Unknown fetchStyle '{$fetchStyle}'");
        }
    }

}
