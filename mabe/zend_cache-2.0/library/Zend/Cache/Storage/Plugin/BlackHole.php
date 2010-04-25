<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage\AbstractPlugin;

class BlackHole extends AbstractPlugin
{

    protected $_writable = true;
    protected $_readable = true;
    
    /**
     * Enables or disables writing to storage.
     * 
     * @param boolean $flag
     * @return Zend\Cache\Storage\Plugin\BlackHole
     */
    public function setWritable($flag)
    {
        $this->_writable = (bool)$flag;
        return $this;
    }
    
    /**
     * Is writing enabled.
     * 
     * @return boolean
     */
    public function getWritable()
    {
        return $this->_writable;
    }
    
    /**
     * Enables or disables reading from storage.
     * 
     * @param boolean $flag
     * @return Zend\Cache\Storage\Plugin\BlackHole
     */
    public function setReadable($flag)
    {
        $this->_readable = (bool)$flag;
        return $this;
    }
    
    /**
     * Is reading enabled.
     * 
     * @return boolean
     */
    public function getReadable()
    {
        return $this->_readable;
    }
    
    public function set($value, $key = null, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->replaceMulti($keyValuePairs, $options);
    }

    public function remove($key = null, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->remove($key, $options);
    }

    public function removeMulti(array $keys, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->removeMulti($keys, $options);
    }

    public function get($key = null, array $options = array())
    {
        if ($this->getReadable() === false) {
            return false;
        }
        
        return $this->getStorage()->get($key, $options);
    }

    public function getMulti(array $keys, array $options = array())
    {
        if ($this->getReadable() === false) {
            return array();
        }
        
        return $this->getStorage()->getMulti($keys, $options);
    }

    public function exists($key = null, array $options = array())
    {
        if ($this->getReadable() === false) {
            return false;
        }
        
        return $this->getStorage()->exists($key, $options);
    }

    public function existsMulti(array $keys, array $options = array())
    {
        if ($this->getReadable() === false) {
            return array();
        }
        
        return $this->getStorage()->existsMulti($keys, $options);
    }

    public function info($key = null, array $options = array())
    {
        if ($this->getReadable() === false) {
            return false;
        }
        
        return $this->getStorage()->info($key, $options);
    }

    public function infoMulti(array $keys, array $options = array())
    {
        if ($this->getReadable() === false) {
            return array();
        }
        
        return $this->getStorage()->infoMulti($keys, $options);
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        if ($this->getReadable() === false) {
            return false;
        }
        
        return $this->getStorage()->getDelayed($keys, $select, $options);
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        if ($this->getReadable() === false) {
            return false;
        }
        
        return $this->getStorage()->fetch($fetchStyle);
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        if ($this->getReadable() === false) {
            return false;
        }
        
        return $this->getStorage()->fetchAll($fetchStyle);
    }

    public function increment($value, $key = null, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->increment($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->incrementMulti($keyValuePairs, $options);
    }

    public function decrement($value, $key = null, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->decrement($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->decrementMulti($keyValuePairs, $options);
    }

    public function touch($key = null, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->touch($key, $options);
    }

    public function touchMulti(array $keys, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->touchMulti($keys, $options);
    }

    public function find($match = Storage::MATCH_ACTIVE, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        if ($this->getReadable() === false) {
            return false;
        }
        
        return $this->getStorage()->find($match, $select, $options);
    }

    public function clear($match = Storage::MATCH_EXPIRED, array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->clear($match, $options);
    }

    public function optimize(array $options = array())
    {
        if ($this->getWritable() === false) {
            return false;
        }
        
        return $this->getStorage()->optimize($options);
    }

}
