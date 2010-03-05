<?php

namespace zend\cache;
use \zend\Options as Options;
use \zend\cache\storageAdapter\StorageAdapterInterface as StorageAdapterInterface;

class CallbackCache
{

    /**
     * The storage adapter
     *
     * @var \zend\cache\storageAdapter\StorageAdapterInterface
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

    public function setStorage(StorageAdapterInterface $storage)
    {
        $this->_storage = $storage;
    }

    public function call($callback, array $args=array(), $options=array())
    {
        // TODO
    }

}
