<?php

namespace Zend\Cache\Storage;

interface Pluggable extends Adaptable
{

    /**
     * Get the "first" storage.
     * - This will be the first storage on a pluggable list
     *   and can be an instance of Zend\Cache\Storage\Pluggable.
     *
     * @return Zend\Cache\Storage\Adaptable
     */
    public function getStorage();

    /**
     * Set the "first" storage.
     * - This will be the first storage on a pluggable list
     *   and can be an instance of Zend\Cache\Storage\Pluggable.
     *
     * @param string|Zend\Cache\Storage\Adaptable $storage
     * @return Zend\Cache\Storage\Pluggable
     */
    public function setStorage(Adaptable $storage);

    /**
     * Get the last "real" storage adapter.
     * - This will be the last storage on a plugable list
     *   and isn't an instance of Zend\Cache\Storage\Pluggable.
     *
     * @return Zend\Cache\Storage\Adaptable
     */
    public function getAdapter();

    /**
     * Set the last "real" storage adapter.
     * - This will be the last storage on a plugable list
     *   and isn't an instance of Zend\Cache\Storage\Pluggable.
     *
     * @param string|Zend\Cache\Storage\Adaptable $adapter
     * @return Zend\Cache\Storage\Pluggable
     */
    public function setAdapter(Adaptable $adapter);

}
