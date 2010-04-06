<?php

namespace Zend\Cache\Storage;

interface Pluggable extends Storable
{

    /**
     * Get storage
     *
     * @return Zend\Cache\Storage\Storable
     */
    public function getStorage();

    /**
     * Set storage
     *
     * @param string|Zend\Cache\Storage\Storable $storage
     * @return Zend\Cache\Storage\Pluggable
     */
    public function setStorage($storage);

    /**
     * Get the main storage
     *
     * @return Zend\Cache\Storage\Storable
     */
    public function getMainStorage();

    /**
     * Set the main storage
     *
     * @param string|Zend\Cache\Storage\Storable $storage
     * @return Zend\Cache\Storage\Pluggable
     */
    public function setMainStorage($storage);

}
