<?php

namespace Zend\Cache;
use \Zend\Cache\Storage\Storable;
use \Zend\Cache\Storage\Pluggable;
use \Zend\Cache\Storage\AbstractPlugin;
use \Zend\Loader\PluginLoader as Loader;
use \Zend\Cache\LoaderException;

class Storage extends AbstractPlugin
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
     * Match tag(s) using OR operator
     *
     * @var int
     */
    const MATCH_TAGS_OR = 000;

    /**
     * Match tag(s) using AND operator
     *
     * @var int
     */
    const MATCH_TAGS_AND = 010;

    /**
     * Negate tag match
     *
     * @var int
     */
    const MATCH_TAGS_NEGATE = 020;

    /**
     * Match tag(s) using OR operator and negates result
     *
     * @var int
     */
    const MATCH_TAGS_OR_NOT = 020;

    /**
     * Match tag(s) using AND operator and negates result
     *
     * @var int
     */
    const MATCH_TAGS_AND_NOT = 030;

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
        while ($storage instanceof Storable) {
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

            $storage = self::pluginFactory($plugin, $options);
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
        $this->setStorage(self::pluginFactory($plugin, $options));

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
        while ($storage instanceof Pluggable) {
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
    public static function adapterFactory($name, $options = array())
    {
        if ($name instanceof Storable) {
            Options::setOptions($name, $options);
            return $name;
        }
/*
        $loader = self::getAdapterLoader();

        try {
            $class = $loader->load($name);
        } catch (\Exception $e) {
            throw new LoaderException("Can't load storage adapter '{$name}'", 0, $e);
        }
*/

        $class = 'Zend\\Cache\\Storage\Adapter\\' . $name;

        return new $class($options);
    }

    /**
     * Get storage adapter loader
     *
     * @return zend\loader\PluginLoader
     */
    public static function getAdapterLoader()
    {
        if (self::$_adapterLoader === null) {
            self::$_adapterLoader = self::_getDefaultAdapterLoader();
        }

        return self::$_adapterLoader;
    }

    /**
     * Set storage adapter loader
     *
     * @param zend\loader\PluginLoader $loader
     * @return void
     */
    public static function setAdapterLoader(Loader $loader)
    {
        self::$_adapterLoader = $loader;
    }

    /**
     * Reset storage adapter loader to default
     *
     * @return void
     */
    public static function resetAdapterLoader()
    {
        self::$_adapterLoader = null;
    }

    /**
     * Get default storage adapter loader
     *
     * @return zend\loader\PluginLoader
     */
    protected static function _getDefaultAdapterLoader()
    {
        $loader = new Loader();
        // @todo: init $loader to load __DIR__ . '/Storage/Sdapter/*' classes
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
    public static function pluginFactory($name, $options = array())
    {
        if ($name instanceof Pluggable) {
            Options::setOptions($name, $options);
            return $name;
        }

/*
        $loader = self::getPluginLoader();

        try {
            $class = $loader->load($name);
        } catch (\Exception $e) {
            throw new LoaderException("Can't load storage plugin '{$name}'", 0, $e);
        }
*/
        $class = 'Zend\\Cache\\Storage\Plugin\\' . $name;

        return new $class($options);
    }

    /**
     * Get storage plugin loader
     *
     * @return zend\loader\PluginLoader
     */
    public static function getPluginLoader()
    {
        if (self::$_pluginLoader === null) {
            self::$_pluginLoader = self::_getDefaultPluginLoader();
        }

        return self::$_pluginLoader;
    }

    /**
     * Set storage plugin loader
     *
     * @param zend\loader\PluginLoader $loader
     * @return void
     */
    public static function setPluginLoader(Loader $loader)
    {
        self::$_pluginLoader = $loader;
    }

    /**
     * Reset storage plugin loader to default
     *
     * @return void
     */
    public static function resetPluginLoader()
    {
        self::$_pluginLoader = null;
    }

    /**
     * Get default storage plugin loader
     *
     * @return zend\loader\PluginLoader
     */
    protected static function _getDefaultPluginLoader()
    {
        $loader = new Loader();
        // @todo: init $loader to load __DIR__ . '/Storage/Plugin/*' classes
        return $loader;
    }

}
