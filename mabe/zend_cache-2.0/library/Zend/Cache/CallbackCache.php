<?php

namespace Zend\Cache;
use \Zend\Options;
use \Zend\Cache\Storage\Storable;

class CallbackCache
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Storable
     */
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

    public function call($callback, array $args=array(), $options=array())
    {
        // TODO
    }

}