<?php

namespace Zend\Cache;
use \Zend\Options;
use \Zend\Cache\Storage\Storable;

class PageCache
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

    public function setStorage(Storable $storage)
    {
        $this->_storage = $storage;
    }

    // old Zend_Cache_Frontend_Page

}
