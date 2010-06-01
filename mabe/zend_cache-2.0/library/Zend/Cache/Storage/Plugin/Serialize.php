<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Serializer\Serializer;
use \Zend\Serializer\Adapter as AdaptableSerializer;

class Serialize extends AbstractPlugin
{

    /**
     * Serializer adapter
     *
     * @var Zend\Serializer\Adapter
     */
    protected $_serializer = null;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['serializer'] = $this->getSerializer();
        return $options;
    }

    /**
     * Get serializer adapter
     *
     * @return Zend\Serializer\Adapter
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
     * @param Zend\Serializer\Adapter
     * @return Zend\Cache\Storage\Plugin\Serialize
     */
    public function setSerializer(AdaptableSerializer $serializer)
    {
        $this->_serializer = $serializer;
        return $this;
    }

    /**
     * Reset serializer adapter to default
     *
     * @return Zend\Cache\Storage\Plugin\Serialize
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
        $value = $this->getSerializer()->serialize($value);
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
        $value = $this->getSerializer()->serialize($value);
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
        $value = $this->getSerializer()->serialize($value);
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

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        $item = $this->getStorage()->fetch($fetchStyle);
        if ($item) {
            $serializer = $this->getSerializer();
            switch ((int)$fetchStyle) {
                case Storage::FETCH_NUM:
                    if (isset($item[1])) {
                        $item[1] = $serializer->unserialize($item[1]);
                    }
                    break;

                case Storage::FETCH_ASSOC:
                    if (isset($item['value'])) {
                        $item['value'] = $serializer->unserialize($item[1]);
                    }
                    break;

                case Storage::FETCH_BOTH:
                    if (isset($item[1])) {
                        $item[1] = $serializer->unserialize($item[1]);
                        $item['value'] = &$item[1];
                    }
                    break;

                case Storage::FETCH_OBJ:
                    if (isset($item->value)) {
                        $item->value = $serializer->unserialize($item->value);
                    }
                    break;

                default:
                    throw new RuntimeException("Unknown fetch style '{$fetchStyle}'");
            }
        }

        return $item;
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        $rs = array();
        while ( ($item = $this->fetch($fetchStyle)) ) {
            $rs[] = &$item;
        }
        return $rs;
    }

    public function increment($value, $key = null, array $options = array())
    {
        $stored = $this->get($key, $options);
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
        $stored = $this->get($key, $options);
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
