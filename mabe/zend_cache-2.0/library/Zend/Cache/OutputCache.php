<?php

namespace Zend\Cache;
use \Zend\Options;
use \Zend\Cache\StorageStorable;

class OutputCache
{

    protected $_storage;

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);

        if (!$this->_storage) {
            throw InvalidArgumentException("Missing option 'storage'");
        }
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
    }

    public function getStorage()
    {
        return $this->_storage;
    }

    public function setStoage(Storable $storage)
    {
        $this->_storage = $storage;
    }

    // old Zend_Cache_Frontend_Output

}
