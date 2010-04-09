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
     * Select item key
     * 
     * @var int
     */
    const SELECT_KEY   = 1;

    /**
     * Select item value
     * 
     * @var int
     */
    const SELECT_VALUE = 2;

    /**
     * Select item key and item value
     * Same as SELECT_KEY | SELECT_VALUE
     *
     * @var int
     */
    const SELECT_KEY_VALUE = 3;

    /**
     * Select item tags
     *
     * @var int
     */
    const SELECT_TAGS  = 4;

    /**
     * Select item mtime
     *
     * @var int
     */
    const SELECT_MTIME = 8;

    /**
     * Select item atime
     */
    const SELECT_ATIME = 16;

    /**
     * Select item ctime
     *
     * @var int
     */
    const SELECT_CTIME = 32;

    /**
     * Fetch item as numeric array
     *
     * @var int
     */
    const FETCH_NUM    = 1;

    /**
     * Fetch item as associative array
     *
     * @var int
     */
    const FETCH_ASSOC  = 2;

    /**
     * Fetch item as array index by both numeric and associative
     *
     * @var int
     */
    const FETCH_BOTH  = 3;

    /**
     * Fetch item as anonymous object
     *
     * @var int
     */
    const FETCH_OBJ = 4;

    /**
     * Adapter class loader
     *
     * @var \Zend\Loader\PluginLoader
     */
    protected static $_adapterLoader;

    /**
     * Plugin class loader
     *
     * @var \Zend\Loader\PluginLoader
     */
    protected static $_pluginLoader;

    /**
     * Get all storage plugins
     *
     * @return Zend\Cache\Storage\Pluggable[]
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
     * @return Zend\Cache\Storage
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
     * @return Zend\Cache\Storage
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
     * @return Zend\Cache\Storage
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
     * @param string|Zend\Cache\Storage\Storable $name
     * @param array|Zend\Config $options
     * @return Zend\Cache\Storage\Storable
     * @throws Zend\Cache\LoaderException
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
     * Get the storage adapter loader
     *
     * @return Zend\Loader\PluginLoader
     */
    public static function getAdapterLoader()
    {
        if (self::$_adapterLoader === null) {
            self::$_adapterLoader = self::_getDefaultAdapterLoader();
        }

        return self::$_adapterLoader;
    }

    /**
     * Set the storage adapter loader
     *
     * @param Zend\Loader\PluginLoader $loader
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
     * Get default adapter loader
     *
     * @return Zend\Loader\PluginLoader
     */
    protected static function _getDefaultAdapterLoader()
    {
        $loader = new Loader();
        // @todo: init $loader to load __DIR__ . '/Storage/Adapter/*' classes
        return $loader;
    }

    /**
     * Instantiate a storage plugin
     *
     * @param string|Zend\Cache\Storage\Pluggable $name
     * @param array|Zend\Config $options
     * @return Zend\Cache\Storage\Pluggable
     * @throws Zend\Cache\LoaderException
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
     * @return Zend\Loader\PluginLoader
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
     * @param Zend\Loader\PluginLoader $loader
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
     * @return Zend\Loader\PluginLoader
     */
    protected static function _getDefaultPluginLoader()
    {
        $loader = new Loader();
        // @todo: init $loader to load __DIR__ . '/Storage/Plugin/*' classes
        return $loader;
    }

}
