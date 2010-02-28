<?php

namespace zend;
use \zend\cache\LoaderException as LoaderException;
use \zend\cache\adapter\AdapterInterface as AdapterInterface;
use \zend\cache\plugin\PluginInterface as PluginInterface;
use \zend\loader\PluginLoader as Loader;

abstract class Cache
{

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

    /* load cache adapter */

    public static function adapterFactory($name, $options)
    {
        if ($name instanceof AdapterInterface) {
            // $name is already a cache adapter object
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

    public static function getAdapterLoader()
    {
        if (self::$_adapterLoader === null) {
            self::$_adapterLoader = self::_getDefaultAdapterLoader();
        }

        return self::$_adapterLoader;
    }

    public static function setAdapterLoader(Loader $loader)
    {
        self::$_adapterLoader = $loader;
    }

    public static function resetAdapterLoader()
    {
        self::$_adapterLoader = null;
    }

    protected static function _getDefaultAdapterLoader()
    {
        $loader = new PluginLoader();
        // @todo: init $loader to load __DIR__ . '/cache/adapter/*' classes
        return $loader;
    }

    /* load cache plugin */

    public static function pluginFactory($name, $options)
    {
        if ($name instanceof PluginInterface) {
            // $name is already a cache plugin object
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

    public static function getPluginLoader()
    {
        if (self::$_pluginLoader === null) {
            self::$_pluginLoader = self::_getDefaultPluginLoader();
        }

        return self::$_pluginLoader;
    }

    public static function setPluginLoader(Loader $loader)
    {
        self::$_pluginLoader = $loader;
    }

    public static function resetPluginLoader()
    {
        self::$_pluginLoader = null;
    }

    protected static function _getDefaultPluginLoader()
    {
        $loader = new PluginLoader();
        // @todo: init $loader to load __DIR__ . '/cache/plugin/*' classes
        return $loader;
    }

}
