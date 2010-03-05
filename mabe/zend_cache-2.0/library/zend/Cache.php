<?php

namespace zend;
use \zend\cache\storageAdapter\StorageAdapterInterface as StorageAdapterInterface;
use \zend\cache\storagePlugin\StoragePluginInterface as StoragePluginInterface;
use \zend\cache\storagePlugin\StoragePluginAbstract as StoragePluginAbstract;
use \zend\loader\PluginLoader as Loader;
use \zend\cache\LoaderException as LoaderException;

class Cache extends StoragePluginAbstract
{

    /**
     * Match expired items
     *
     * @var int
     */
    const MATCH_EXPIRED = 01;

    /**
     * Match active items
     *
     * @var int
     */
    const MATCH_ACTIVE = 02;

    /**
     * Match active and expired items
     *
     * @var int
     */
    const MATCH_ALL = 03;

    /**
     * Match items by tag(s) using AND operator
     *
     * @var int
     */
    const MATCH_TAGS_AND = 010;

    /**
     * Match items by tag(s) using OR operator
     *
     * @var int
     */
    const MATCH_TAGS_OR = 020;

    /**
     * Match items where no given tag(s) matches
     *
     * @var int
     */
    const MATCH_TAGS_NONE = 030;

    /**
     * Storage adapter class loader
     *
     * @var \zend\loader\PluginLoader
     */
    protected static $_storageAdapterLoader;

    /**
     * Storage plugin class loader
     *
     * @var \zend\loader\PluginLoader
     */
    protected static $_storagePluginLoader;

    /**
     * Get all storage plugins
     *
     * @return zend\cache\storagePlugin\StoragePluginInterface[]
     */
    public function getPlugins()
    {
        $plugins = array();
        $storage = $this->getStorage();
        while ($storage instanceof StoragePluginInterface) {
            $plugins[] = $storage;
            $storage = $storage->getStorage();
        }
        return $plugins;
    }

    /**
     * Reset all storage plugins
     *
     * @param string[] $plugins
     * @return zend\Cache
     */
    public function setPlugins(array $plugins)
    {
        // set given plugins on the main storage adapter
        $storage = $this->getMainStorage();
        foreach ($plugins as $k => $v) {
            if (is_string($k)) {
                $plugin  = $k;
                $options = array('storage' => $storage) + $v;
            } else {
                $plugin  = $v;
                $options = array('storage' => $storage);
            }

            $storage = self::storagePluginFactory($plugin, $options);
        }
        $this->setStorage($storage);

        return $this;
    }

    /**
     * Add a storage plugin
     *
     * @param string $plugin
     * @param array $options
     * @return zend\Cache
     */
    public function addPlugin($plugin, array $options = array())
    {
        $options = array('storage' => $this->getStorage()) + $options;
        $this->setStorage(self::storagePluginFactory($plugin, $options));

        return $this;
    }

    /**
     * Remove a storage plugin
     *
     * @param string $plugin Class of the plugin to remove
     * @return zend\Cache
     */
    public function removePlugin($plugin)
    {
        // get all storage plugins up to the given plugin
        $plugins = array();
        $storage = $this->getStorage();
        $found   = false;
        while ($storage instanceof StoragePluginInterface) {
            if (get_class($storage) != $plugin) {
                $plugins[] = $storage;
                $storage = $storage->getStorage();
            } else {
                $found = true;
                $storage = $storage->getStorage();
                break;
            }
        }

        // if the given plugin wasn't found throw an exception
        if ($found === false) {
            throw new InvalidArgumentException("Storage plugin '{$plugin}' not found");
        }

        // reset plugins
        foreach ($plugins as $plugin) {
            $plugin->setStorage($storage);
            $storage = $plugin;
        }
        $this->setStorage($storage);

        return $this;
    }

    /* load cache adapter */

    /**
     * Instantiate a storage adapter
     *
     * @param string|zend\cache\storageAdapter\StorageAdapterInterface $name
     * @param array|zend\Config $options
     * @return zend\cache\storageAdapter\StorageAdapterInterface
     * @throws zend\cache\LoaderException
     */
    public static function storageAdapterFactory($name, $options = array())
    {
        if ($name instanceof StorageAdapterInterface) {
            // $name is already a storage adapter object
            Options::setOptions($name, $options);
            return $name;
        }
/*
        $loader = self::getStorageAdapterLoader();

        try {
            $class = $loader->load($name);
        } catch (\Exception $e) {
            throw new LoaderException("Can't load storage adapter '{$name}'", 0, $e);
        }
*/

        $class = 'zend\\cache\\storageAdapter\\' . $name;

        return new $class($options);
    }

    /**
     * Get storage adapter loader
     *
     * @return zend\loader\PluginLoader
     */
    public static function getStorageAdapterLoader()
    {
        if (self::$_storageAdapterLoader === null) {
            self::$_storageAdapterLoader = self::_getDefaultStorageAdapterLoader();
        }

        return self::$_storageAdapterLoader;
    }

    /**
     * Set storage adapter loader
     *
     * @param zend\loader\PluginLoader $loader
     * @return void
     */
    public static function setStorageAdapterLoader(Loader $loader)
    {
        self::$_storageAdapterLoader = $loader;
    }

    /**
     * Reset storage adapter loader to default
     *
     * @return void
     */
    public static function resetStorageAdapterLoader()
    {
        self::$_storageAdapterLoader = null;
    }

    /**
     * Get default storage adapter loader
     *
     * @return zend\loader\PluginLoader
     */
    protected static function _getDefaultStorageAdapterLoader()
    {
        $loader = new PluginLoader();
        // @todo: init $loader to load __DIR__ . '/cache/adapter/*' classes
        return $loader;
    }

    /**
     * Instantiate a storage plugin
     *
     * @param string|zend\cache\storagePlugin\StoragePluginInterface $name
     * @param array|zend\Config $options
     * @return zend\cache\storagePlugin\StoragePluginInterface
     * @throws zend\cache\LoaderException
     */
    public static function storagePluginFactory($name, $options = array())
    {
        if ($name instanceof StoragePluginInterface) {
            // $name is already a storage plugin object
            Options::setOptions($name, $options);
            return $name;
        }

/*
        $loader = self::getStoragePluginLoader();

        try {
            $class = $loader->load($name);
        } catch (\Exception $e) {
            throw new LoaderException("Can't load storage plugin '{$name}'", 0, $e);
        }
*/
        $class = 'zend\\cache\\storagePlugin\\' . $name;

        return new $class($options);
    }

    /**
     * Get storage plugin loader
     *
     * @return zend\loader\PluginLoader
     */
    public static function getStoragePluginLoader()
    {
        if (self::$_storagePluginLoader === null) {
            self::$_storagePluginLoader = self::_getDefaultStoragePluginLoader();
        }

        return self::$_storagePluginLoader;
    }

    /**
     * Set storage plugin loader
     *
     * @param zend\loader\PluginLoader $loader
     * @return void
     */
    public static function setStoragePluginLoader(Loader $loader)
    {
        self::$_storagePluginLoader = $loader;
    }

    /**
     * Reset storage plugin loader to default
     *
     * @return void
     */
    public static function resetStoragePluginLoader()
    {
        self::$_storagePluginLoader = null;
    }

    /**
     * Get default storage plugin loader
     *
     * @return zend\loader\PluginLoader
     */
    protected static function _getDefaultStoragePluginLoader()
    {
        $loader = new PluginLoader();
        // @todo: init $loader to load __DIR__ . '/cache/plugin/*' classes
        return $loader;
    }

}
