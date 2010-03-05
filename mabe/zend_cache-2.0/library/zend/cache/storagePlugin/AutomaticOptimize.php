<?php

namespace zend\cache\storagePlugin;
use \zend\cache\InvalidArgumentAxception as InvalidArgumentAxception;

class AutomaticOptimize extends StoragePluginAbstract
{

    /**
     * Automatic optimizing factor
     *
     * @var int
     */
    protected $_optimizingFactor = 0;

    /**
     * Get automatic optimizing factor
     *
     * @return int
     */
    public function getOptimizingFactor()
    {
        return $this->_optimizingFactor;
    }

    /**
     * Set automatic optimizing factor
     *
     * @param int $factor
     * @return \zend\cache\plugin\AutomaticOptimize
     */
    public function setOptimizingFactor($factor)
    {
        $factor = (int)$factor;
        if ($factor < 0) {
            throw new InvalidArgumentAxception("Invalid optimizing factor '{$factor}': must be greater or equal 0");
        }
        $this->_optimizingFactor = $factor;

        return $this;
    }

    public function remove($key = null, array $options = array())
    {
        $ret = $this->getStorage()->remove($key, $options);

        if ($ret === true) {
            $this->_optimizeByFactor($this->getOptimizingFactor(), $options);
        }

        return $ret;
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $ret = $this->getStorage()->removeMulti($keys, $options);

        if ($ret === true) {
            $this->_optimizeByFactor($this->getOptimizingFactor(), $options);
        }

        return $ret;
    }

    public function clear($match, array $options = array())
    {
        $ret = $this->getStorage()->clear($match, $options);

        if ($ret === true) {
            $this->_optimizeByFactor($this->getOptimizingFactor(), $options);
        }

        return $ret;
    }

    /**
     * Call optimize by factor
     *
     * @param int $factor
     * @param array $options
     * @return boolean
     */
    protected function _optimizeByFactor($factor, array $options)
    {
        if ($factor > 0) {
            $rand = mt_rand(1, $factor);
            if ($rand == 1) {
                return $this->optimize($options);
            }
        }

        return true;
    }

}
