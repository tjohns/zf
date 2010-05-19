<?php

namespace Zend\Cache;
use \Zend\Cache\Storage\Storable;
use \Zend\Cache\Storage\Pluggable;
use \Zend\Cache\Storage\Plugin\AbstractPlugin;
use \Zend\Loader\PluginLoader as Loader;
use \Zend\Cache\LoaderException;
use \Zend\Options;

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
        while ($storage instanceof Pluggable) {
            $plugins[] = $storage;
            $storage = $storage->getStorage();
        }
        return $plugins;
    }

    /**
     * Reset all storage plugins
     *
     * @param array $plugins
     * @return Zend\Cache\Storage
     */
    public function setPlugins(array $plugins)
    {
        $this->setStorage(self::factory(array(
            'adapter' => $this->getAdapter(),
            'plugins' => $plugins
        )));

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
        $options['storage'] = $this->getStorage();
        $this->setStorage(self::pluginFactory($plugin, $options));

        return $this;
    }

    /**
     * Add a list of storage plugins
     *
     * @param array $plugins
     * @return Zend\Cache\Storage
     */
    public function addPlugins(array $plugins)
    {
        $this->setStorage(self::factory(array(
            'adapter' => $this->getStorage(),
            'plugins' => $plugins
        )));

        return $this;
    }

    /**
     * Removes all storage plugins
     *
     * @return Zend\Cache\Storage
     */
    public function removePlugins()
    {
        $this->setStorage($this->getAdapter());
        return $this;
    }

    /**
     * Remove a storage plugin
     *
     * @param string $plugin Class of the plugin to remove
     * @return Zend\Cache\Storage
     */
    public function removePlugin(Pluggable $plugin)
    {
        if ($plugin === $this) {
            throw new InvalidArgumentException('Can\'t remove $this');
        }

        $plugins = $this->getPlugins();
        for ($i = 0, $max = count($plugins); $i < $max; $i++) {
            if ($plugins[$i] === $plugin) {
                if (isset($plugins[$i-1])) {
                    $plugins[$i-1]->setStorage($plugin->getStorage());
                } else {
                    $this->setStorage($plugin->getStorage());
                }

                return $this;
            }
        }

        $pluginClass = get_class($plugin);
        $pluginHash  = spl_object_hash($plugin);
        throw new InvalidArgumentException(
            "Storage plugin 'object({$pluginClass})#{$pluginHash}' not found"
        );
    }

    /* factories */

    /**
     * The storage factory
     * This can instantiate storage adapters and plugins.
     *
     * @param array|\Zend\Config $cfg
     */
    public static function factory($cfg)
    {
        if ($cfg instanceof \Zend\Config) {
            $cfg = $cfg->toArray();
        } elseif (!is_array($cfg)) {
            throw new InvalidArgumentException(
                'The factory needs an instance of \\Zend\\Config '
              . 'or an associative array as argument'
            );
        }

        // instantiate the adapter
        if (!isset($cfg['adapter'])) {
            throw new InvalidArgumentException(
                'Missing "adapter"'
            );
        } elseif (is_array($cfg['adapter'])) {
            if (!isset($cfg['adapter']['name'])) {
                throw new InvalidArgumentException(
                    'Missing "adapter.name"'
                );
            }

            $name    = $cfg['adapter']['name'];
            $options = isset($cfg['adapter']['options'])
                     ? $cfg['adapter']['options'] : array();
            $adapter = self::adapterFactory($name, $options);
        } else {
            $adapter = self::adapterFactory($cfg['adapter']);
        }

        // add plugins
        if (isset($cfg['plugins'])) {
            if (!is_array($cfg['plugins'])) {
                throw new InvalidArgumentException(
                    'Plugins needs to be an array'
                );
            }

            foreach ($cfg['plugins'] as $k => $v) {
                if (is_string($k)) {
                    $name = $k;
                    if (!is_array($v)) {
                        throw new InvalidArgumentException(
                            '"plugins.'.$k.'" needs to be an array'
                        );
                    }
                    $options = $v;
                    $options['storage'] = $adapter;
                } else {
                    $name    = $v;
                    $options = array('storage' => $adapter);
                }

                $adapter = self::pluginFactory($name, $options);
            }
        }

        // set adapter or plugin options
        if (isset($cfg['options'])) {
            if (!is_array($cfg['options'])) {
                throw new InvalidArgumentException(
                    'Options needs to be an array'
                );
            }

            $adapter->setOptions($options);
        }

        return $adapter;
    }

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
