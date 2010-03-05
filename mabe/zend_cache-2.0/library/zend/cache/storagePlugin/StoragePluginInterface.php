<?php

namespace zend\cache\storagePlugin;
use \zend\cache\storageAdapter\StorageAdapterInterface as StorageAdapterInterface;

interface StoragePluginInterface extends StorageAdapterInterface
{

    /**
     * Get storage
     *
     * @return zend\cache\storageAdapter\StorageAdapterInterface
     */
    public function getStorage();

    /**
     * Set storage
     *
     * @param zend\cache\storageAdapter\StorageAdapterInterface $storage
     * @return zend\cache\storagePlugin\StoragePluginInterface
     */
    public function setStorage(StorageAdapterInterface $storage);

    /**
     * Get the main storage
     *
     * @return zend\cache\storageAdapter\StorageAdapterInterface
     */
    public function getMainStorage();

    /**
     * Set the main storage
     *
     * @param zend\cache\storageAdapter\StorageAdapterInterface $storage
     * @return zend\cache\storagePlugin\StoragePluginInterface
     */
    public function setMainStorage(StorageAdapterInterface $storage);

}
