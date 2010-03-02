<?php

namespace zend\cache\plugin;
use \zend\Serializer as Serializer;
use \zend\serializer\adapter\AdapterInterface as SerializerAdapterInterface;

class Serialize extends PluginAbstract
{

    /**
     * Serializer adapter
     *
     * @var \zend\serializer\adapter\AdapterInterface
     */
    protected $_serializer = null;

    public function getSerializer()
    {
        if ($this->_serializer === null) {
            return Serializer::getDefaultAdapter();
        }

        return $this->_serializer;
    }

    public function setSerializer(SerializerAdapterInterface $serializer)
    {
        $this->_serializer = $serializer;
    }

    public function resetSerializer()
    {
        $this->_serializer = null;
    }

    public function getCapabilities()
    {
        $capabilities = $this->_innerAdapter->getCapabilities();
        $capabilities['serialize'] = true;
        return $capabilities;
    }

    public function set($value, $key = null, array $options = array())
    {
        $value = $this->getSerializer()->serialize();
        $this->getAdapter()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $serializer = $this->getSerializer();
        foreach ($keyValuePairs as &$value) {
            $value = $serializer->serialize($value);
        }

        return $this->getAdapter()->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array())
    {
        $value = $this->getSerializer()->serialize();
        $this->getAdapter()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $serializer = $this->getSerializer();
        foreach ($keyValuePairs as &$value) {
            $value = $serializer->serialize($value);
        }

        return $this->getAdapter()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array())
    {
        $value = $this->getSerializer()->serialize();
        $this->getAdapter()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $serializer = $this->getSerializer();
        foreach ($keyValuePairs as &$value) {
            $value = $serializer->serialize($value);
        }

        return $this->getAdapter()->replaceMulti($keyValuePairs, $options);
    }

    public function get($key = null, array $options = array())
    {
        $rs = $this->getAdapter()->get($key, $options);
        return $this->getSerializer()->unserialize($rs);
    }

    public function getMulti(array $keys, array $options = array())
    {
        $rsList = $this->getAdapter()->getMulti($keys, $options);

        $serializer = $this->getSerializer();
        foreach ($rsList as &$value) {
            $value = $serializer->unserialize($value);
        }

        return $rsList;
    }

    public function increment($value, $key = null, array $options = array())
    {
        $adapter    = $this->getAdapter();
        $serializer = $this->getSerializer();
        $stored     = $adapter->get($key, $options);
        $value      = (int)$value;

        if ($stored === false) {
            $value = $serializer->serialize($value);
        } else {
            $stored = $serializer->unserialize($stored);
            $value  = $serializer->serialize($value + $stored);
        }

        return $adapter->set($value, $key, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $adapter    = $this->getAdapter();
        $serializer = $this->getSerializer();

        $storedList = $adapter->getMulti(array_keys($keyValuePairs), $options);
        foreach ($keyValuePairs as $key => &$value) {
            $value = (int)$value;
            if (!isset($storedList[$key])) {
                $value = $serializer->serialize($value);
            } else {
                $stored = $serializer->unserialize($storedList[$key]);
                $value  = $serializer->serialize($value + $stored);
            }
        }

        return $this->setMulti($keyValuePairs, $options);
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $adapter    = $this->getAdapter();
        $serializer = $this->getSerializer();
        $stored     = $adapter->get($key, $options);
        $value      = (int)$value;

        if ($stored === false) {
            $value = $serializer->serialize($value);
        } else {
            $stored = $serializer->unserialize($stored);
            $value  = $serializer->serialize($value - $stored);
        }

        return $adapter->set($value, $key, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $adapter    = $this->getAdapter();
        $serializer = $this->getSerializer();

        $storedList = $adapter->getMulti(array_keys($keyValuePairs), $options);
        foreach ($keyValuePairs as $key => &$value) {
            $value = (int)$value;
            if (!isset($storedList[$key])) {
                $value = $serializer->serialize($value);
            } else {
                $stored = $serializer->unserialize($storedList[$key]);
                $value  = $serializer->serialize($value - $stored);
            }
        }

        return $this->setMulti($keyValuePairs, $options);
    }

}
