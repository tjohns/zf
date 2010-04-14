<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;
use \Zend\Cache\Storage\AbstractPlugin;
use \Zend\Cache\InvalidArgumentAxception;

class ClearByFactor extends AbstractPlugin
{

    /**
     * Automatic clearing factor
     *
     * @var int
     */
    protected $_clearingFactor = 0;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['clearingFactor'] = $this->getClearingFactor();
        return $options;
    }

    /**
     * Get automatic clearing factor
     *
     * @return int
     */
    public function getClearingFactor()
    {
        return $this->_clearingFactor;
    }

    /**
     * Set automatic clearing factor
     *
     * @param int $factor
     * @return Zend\Cache\Storage\Plugin\AutomaticOptimize
     */
    public function setClearingFactor($factor)
    {
        $factor = (int)$factor;
        if ($factor < 0) {
            throw new InvalidArgumentAxception("Invalid clearing factor '{$factor}': must be greater or equal 0");
        }
        $this->_clearingFactor = $factor;

        return $this;
    }

    public function set($value, $key = null, array $options = array())
    {
        $ret = $this->getStorage()->set($value, $key, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $ret = $this->getStorage()->setMulti($keyValuePairs, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function add($value, $key = null, array $options = array())
    {
        $ret = $this->getStorage()->add($value, $key, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $ret = $this->getStorage()->addMulti($keyValuePairs, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function replace($value, $key = null, array $options = array())
    {
        $ret = $this->getStorage()->replace($value, $key, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $ret = $this->getStorage()->replaceMulti($keyValuePairs, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    protected function _clearByFactor($factor, array $options)
    {
        if ($factor > 0 && mt_rand(1, $factor) == 1) {
            return $this->clear(Storage::MATCH_EXPIRED, $options);
        }

        return true;
    }

}
