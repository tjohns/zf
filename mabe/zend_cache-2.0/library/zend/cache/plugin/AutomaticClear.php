<?php

namespace zend\cache\plugin;
use \zend\Cache as Cache;
use \zend\cache\InvalidArgumentAxception as InvalidArgumentAxception;

class AutomaticClear extends PluginAbstract
{

    /**
     * Automatic clearing factor
     *
     * @var int
     */
    protected $_clearingFactor = 0;

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
     * @return \zend\cache\plugin\AutomaticOptimize
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
        $ret = $this->set($value, $key, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $ret = $this->setMulti($keyValuePairs, $options);

        if ($ret === true) {
            $this->_optimizeByFactor($this->getOptimizingFactor(), $options);
        }

        return $ret;
    }

    public function add($value, $key = null, array $options = array())
    {
        $ret = $this->add($value, $key, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $ret = $this->addMulti($keyValuePairs, $options);

        if ($ret === true) {
            $this->_optimizeByFactor($this->getOptimizingFactor(), $options);
        }

        return $ret;
    }

    public function replace($value, $key = null, array $options = array())
    {
        $ret = $this->replace($value, $key, $options);

        if ($ret === true) {
            $this->_clearByFactor($this->getClearingFactor(), $options);
        }

        return $ret;
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $ret = $this->replaceMulti($keyValuePairs, $options);

        if ($ret === true) {
            $this->_optimizeByFactor($this->getOptimizingFactor(), $options);
        }

        return $ret;
    }

    protected function _clearByFactor($factor, array $options)
    {
        if ($factor > 0) {
            $rand = mt_rand(1, $factor);
            if ($rand == 1) {
                return $this->clear(Cache::MATCH_EXPIRED, $options);
            }
        }

        return true;
    }

}
