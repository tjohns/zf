<?php

namespace zend\cache\storagePlugin;
use \zend\cache\storageAdapter\StorageAdapterInterface as StorageAdapterInterface;

class Levels extends StoragePluginAbstract
{

    protected $_storages = array();

    public function setStorage(StorageAdapterInterface $storage)
    {
        parent::setStorage($storage);

        // The inner storage is the first storage
        $this->_storages[0] = $this->getStorage();
    }

    // @todo: handle different priorities
    public function appendStorage(StorageAdapterInterface $storage)
    {
        $this->_storages[] = $storage;
    }

    /**
     * Get minimum capabilities of all append storages
     *
     * {@inherit}
     */
    public function getCapabilities()
    {
        $capabilities = array();

        foreach ($this->_storages as $storage) {
            foreach ($storage->getCapabilities() as $k => $v) {
                if (!isset($capabilities[$k])) {
                    $capabilities[$k] = $v;
                } elseif ($capabilities[$k] === true) {
                    $capabilities[$k] = $v;
                }
            }
        }

        return $capabilities;
    }

    // on read:  read from all storages and on hit break iteration and return result
    // on write: write to all storages (if enough space)

}
