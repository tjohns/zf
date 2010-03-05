<?php

namespace zend\cache\storagePlugin;
use \zend\Serializer as Serializer;
use \zend\serializer\adapter\AdapterInterface as SerializerAdapterInterface;

class Serialize extends StoragePluginAbstract
{

    /**
     * Serializer adapter
     *
     * @var \zend\serializer\adapter\AdapterInterface
     */
    protected $_serializer = null;

    /**
     * Get serializer adapter
     *
     * @return \zend\serializer\adapter\AdapterInterface
     */
    public function getSerializer()
    {
        if ($this->_serializer === null) {
            return Serializer::getDefaultAdapter();
        }

        return $this->_serializer;
    }

    /**
     * Set serializer adapter
     *
     * @param \zend\serializer\adapter\AdapterInterface
     * @return \zend\cache\plugin\Serialize
     */
    public function setSerializer(SerializerAdapterInterface $serializer)
    {
        $this->_serializer = $serializer;
        return $this;
    }

    /**
     * Reset serializer adapter to default
     *
     * @return \zend\cache\plugin\Serialize
     */
    public function resetSerializer()
    {
        $this->_serializer = null;
        return $this;
    }

    public function getCapabilities()
    {
        $capabilities = $this->getStorage->getCapabilities();
        $capabilities['serialize'] = true;
        return $capabilities;
    }

    public function set($value, $key = null, array $options = array())
    {
        $value = $this->getSerializer()->serialize();
        $this->getStorage()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $serializer = $this->getSerializer();
        foreach ($keyValuePairs as &$value) {
            $value = $serializer->serialize($value);
        }

        return $this->getStorage()->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array())
    {
        $value = $this->getSerializer()->serialize();
        $this->getStorage()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $serializer = $this->getSerializer();
        foreach ($keyValuePairs as &$value) {
            $value = $serializer->serialize($value);
        }

        return $this->getStorage()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array())
    {
        $value = $this->getSerializer()->serialize();
        $this->getStorage()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $serializer = $this->getSerializer();
        foreach ($keyValuePairs as &$value) {
            $value = $serializer->serialize($value);
        }

        return $this->getStorage()->replaceMulti($keyValuePairs, $options);
    }

    public function get($key = null, array $options = array())
    {
        $rs = $this->getStorage()->get($key, $options);
        return $this->getSerializer()->unserialize($rs);
    }

    public function getMulti(array $keys, array $options = array())
    {
        $rsList = $this->getStorage()->getMulti($keys, $options);

        $serializer = $this->getSerializer();
        foreach ($rsList as &$value) {
            $value = $serializer->unserialize($value);
        }

        return $rsList;
    }

    public function increment($value, $key = null, array $options = array())
    {
        $stored     = $this->get($key, $options);
        $this->set((int)$stored + (int)$value, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $storedList = $this->getMulti(array_keys($keyValuePairs), $options);
        foreach ($keyValuePairs as $key => &$value) {
            $stored = isset($storedList[$key]) ? (int)$storedList[$key] : 0;
            $value  = $stored + (int)$value;
        }
        return $this->setMulti($keyValuePairs, $options);
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $stored     = $this->get($key, $options);
        $this->set((int)$stored - (int)$value, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $storedList = $this->getMulti(array_keys($keyValuePairs), $options);
        foreach ($keyValuePairs as $key => &$value) {
            $stored = isset($storedList[$key]) ? (int)$storedList[$key] : 0;
            $value  = $stored - (int)$value;
        }
        return $this->setMulti($keyValuePairs, $options);
    }

}
