<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;
use \Zend\Cache\InvalidArgumentException;

class Reluctant extends AbstractPlugin
{

    /**
     * The threshold number of required set/add calls to write the item
     * NOTE: A value of 1 disables this plugin and no calls will be counted.
     *
     * @var int
     */
    protected $_reluctantThreshold = 1;

    /**
     * The cache storage save reluctant counter of set/add calls
     *
     * @var \Zend\Cache\Storage\Adaptable
     */
    protected $_reluctantStorage;

    /**
     * The cache key prefix used to store reluctant counter
     *
     * @var string
     */
    protected $_reluctantKeyPrefix = 'ZFCacheStoragePluginReluctant';

    public function __construct($options)
    {
        parent::__construct($options);

        if (!$this->getReluctantStorage()) {
            throw new InvalidArgumentException("Missing option 'reluctantStorage'");
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['reluctantThreshold'] = $this->getReluctantThreshold();
        $options['reluctantStorage']   = $this->getReluctantStorage();
        $options['reluctantKeyPrefix'] = $this->getReluctantKeyPrefix();
        return $options;
    }

    public function getReluctantThreshold()
    {
        return $this->_reluctantThreshold;
    }

    public function setReluctantThreshold($number)
    {
        $number = (int)$number;
        if ($number < 1) {
            throw new InvalidArgumentException("Invalid threshold '{$number}': must be greater than 0");
        }

        $this->reluctantThreshold = $number;
        return $this;
    }

    public function getReluctantStorage()
    {
        return $this->_reluctantStorage;
    }

    public function setReluctantStorage(Storage\Adaptable $storage)
    {
        $this->_reluctantStorage = $storage;
        return $this;
    }

    public function getReluctantKeyPrefix()
    {
        return $this->reluctantKeyPrefix;
    }

    public function setReluctantKeyPrefix($key)
    {
        $this->reluctantKeyPrefix = (string)$key;
        return $this;
    }

    public function set($value, $key = null, array $options = array())
    {
        $threshold = $this->getReluctantThreshold();
        if ($threshold > 1) {
            $reluctantStorage = $this->getReluctantStorage();
            $reluctantKey     = $this->getReluctantKeyPrefix().$this->_key($key);
            $reluctantCounter = (int)$reluctantStorage->get($reluctantKey, $options);
            ++$reluctantCounter;

            if ($reluctantCounter < $threshold) {
                $ret = $this->getStorage()->set($value, $key, $options);
                $reluctantCounter = 0;
            } else {
                $ret = true;
            }

            $reluctantStorage->set($reluctantCounter, $reluctantKey, $options);

            return $ret;
        }

        return $this->getStorage()->set($value, $key, $options);
    }

    public function add($value, $key = null, array $options = array())
    {
        $threshold = $this->getReluctantThreshold();
        if ($threshold > 1) {
            $reluctantStorage = $this->getReluctantStorage();
            $reluctantKey     = $this->getReluctantKeyPrefix().$this->_key($key);
            $reluctantCounter = (int)$reluctantStorage->get($reluctantKey, $options);
            ++$reluctantCounter;

            if ($reluctantCounter < $threshold) {
                $ret = $this->getStorage()->add($value, $key, $options);
                $reluctantCounter = 0;
            } else {
                $ret = true;
            }

            $reluctantStorage->set($reluctantCounter, $reluctantKey, $options);

            return $ret;
        }

        return $this->getStorage()->add($value, $key, $options);
    }

}
