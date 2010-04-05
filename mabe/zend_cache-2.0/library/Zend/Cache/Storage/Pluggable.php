<?php

namespace Zend\Cache\Storage;

interface Pluggable extends Storable
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
     * @param string|zend\cache\storageAdapter\StorageAdapterInterface $storage
     * @return zend\cache\storagePlugin\StoragePluginInterface
     */
    public function setStorage($storage);

    /**
     * Get the main storage
     *
     * @return zend\cache\storageAdapter\StorageAdapterInterface
     */
    public function getMainStorage();

    /**
     * Set the main storage
     *
     * @param string|zend\cache\storageAdapter\StorageAdapterInterface $storage
     * @return zend\cache\storagePlugin\StoragePluginInterface
     */
    public function setMainStorage($storage);

}
