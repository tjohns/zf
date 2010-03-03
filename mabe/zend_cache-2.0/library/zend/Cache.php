<?php

namespace zend;
use \zend\cache\adapter\AdapterInterface as AdapterInterface;
use \zend\cache\plugin\PluginInterface as PluginInterface;
use \zend\cache\plugin\PluginAbstract as PluginAbstract;
use \zend\loader\PluginLoader as Loader;
use \zend\cache\LoaderException as LoaderException;

class Cache extends PluginAbstract
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
     * Cache adapter class loader
     *
     * @var \zend\loader\PluginLoader
     */
    protected static $_adapterLoader;

    /**
     * Cache plugin class loader
     *
     * @var \zend\loader\PluginLoader
     */
    protected static $_pluginLoader;

    /**
     * Get all used plugins
     *
     * @return zend\cache\plugin\PluginInterface[]
     */
    public function getPlugins()
    {
        $plugins = array();
        $adapter = $this->getAdapter();
        while ($adapter instanceof PluginInterface) {
            $plugins[] = $adapter;
            $adapter = $this->getAdapter();
        }
        return $plugins;
    }

    /**
     * Replace all used plugins by the given plugins
     *
     * @param array $plugins
     * @return zend\Cache
     */
    public function setPlugins(array $plugins)
    {
        // get the main adapter
        $adapter = $this->getAdapter();
        while ($adapter instanceof PluginInterface) {
            $adapter = $adapter->getAdapter();
        }

        // set given plugins on the main adapter
        foreach ($plugins as $k => $v) {
            if (is_string($k)) {
                $plugin  = $k;
                $options = array('adapter' => $adapter) + $v;
            } else {
                $plugin  = $v;
                $options = array('adapter' => $adapter);
            }

            $adapter = self::pluginFactory($plugin, $options);
        }

        // backport the inner adapter
        $this->_adapter = $adapter;

        return $this;
    }

    /**
     * Add the given plugin
     *
     * @param string $plugin
     * @param array $options
     * @return zend\Cache
     */
    public function addPlugin($plugin, array $options = array())
    {
        $options = array('adapter' => $this->getAdapter()) + $options;
        $this->_adapter = self::pluginFactory($plugin, $options);
        return $this;
    }

    /**
     * Remove one plugin
     *
     * @param string $plugin Class of the plugin to remove
     * @return zend\Cache
     */
    public function removePlugin($plugin)
    {
        // get all plugins up to the given plugin
        $plugins = array();
        $adapter = $this->getAdapter();
        $found   = false;
        while ($adapter instanceof PluginInterface) {
            if (get_class($adapter) != $plugin) {
                $plugins[] = $adapter;
                $adapter = $adapter->getAdapter();
            } else {
                $found = true;
                $adapter = $adapter->getAdapter();
                break;
            }
        }

        // if the given plugin wasn't found throw an exception
        if ($found === false) {
            throw new InvalidArgumentException("Given plugin '{$plugin}' wasn't found");
        }

        // reset plugins
        foreach ($plugins as $plugin) {
            $plugin->setAdapter($adapter);
            $adapter = $plugin;
        }
        $this->_adapter = $adapter;

        return $this;
    }

    /* load cache adapter */

    /**
     * Instantiate an adapter
     *
     * @param string|zend\cache\adapter\AdapterInterface $name
     * @param array|zend\Config $options
     * @return zend\cache\adapter\AdapterInterface
     * @throws zend\cache\LoaderException
     */
    public static function adapterFactory($name, $options = array())
    {
        if ($name instanceof AdapterInterface) {
            // $name is already an adapter object
            Options::setOptions($name, $options);
            return $name;
        }

        $loader = self::getAdapterLoader();

        try {
            $class = $loader->load($name);
        } catch (\Exception $e) {
            throw new LoaderException('Can\'t load cache adapter "' . $name . '"', 0, $e);
        }

        return new $class($options);
    }

    /**
     * Get adapter loader
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
     * Set adapter loader
     *
     * @param zend\loader\PluginLoader $loader
     * @return void
     */
    public static function setAdapterLoader(Loader $loader)
    {
        self::$_adapterLoader = $loader;
    }

    /**
     * Reset the adapter loader to default
     *
     * @return void
     */
    public static function resetAdapterLoader()
    {
        self::$_adapterLoader = null;
    }

    /**
     * Get the default adapter loader
     *
     * @return zend\loader\PluginLoader
     */
    protected static function _getDefaultAdapterLoader()
    {
        $loader = new PluginLoader();
        // @todo: init $loader to load __DIR__ . '/cache/adapter/*' classes
        return $loader;
    }

    /* load cache plugin */

    /**
     * Instantiate a plugin
     *
     * @param string|zend\cache\plugin\PluginInterface $name
     * @param array|zend\Config $options
     * @return zend\cache\plugin\PluginInterface
     * @throws zend\cache\LoaderException
     */
    public static function pluginFactory($name, $options = array())
    {
        if ($name instanceof PluginInterface) {
            // $name is already a plugin object
            Options::setOptions($name, $options);
            return $name;
        }

        $loader = self::getPluginLoader();

        try {
            $class = $loader->load($name);
        } catch (\Exception $e) {
            throw new LoaderException('Can\'t load cache plugin "' . $name . '"', 0, $e);
        }

        return new $class($options);
    }

    /**
     * Get plugin loader
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
     * Set plugin loader
     *
     * @param zend\loader\PluginLoader $loader
     * @return void
     */
    public static function setPluginLoader(Loader $loader)
    {
        self::$_pluginLoader = $loader;
    }

    /**
     * Reset plugin loader to default
     *
     * @return void
     */
    public static function resetPluginLoader()
    {
        self::$_pluginLoader = null;
    }

    /**
     * Get default plugin loader
     *
     * @return zend\loader\PluginLoader
     */
    protected static function _getDefaultPluginLoader()
    {
        $loader = new PluginLoader();
        // @todo: init $loader to load __DIR__ . '/cache/plugin/*' classes
        return $loader;
    }

}
