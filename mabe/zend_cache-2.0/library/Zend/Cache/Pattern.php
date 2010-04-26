<?php

namespace Zend\Cache;
use \Zend\Cache\Pattern\PatternInterface;
use \Zend\Loader\PluginLoader as Loader;
use \Zend\Cache\LoaderException;

class Pattern
{

    /**
     * The pattern loader
     *
     * @var null|Zend\Loader\PluginLoader
     */
    protected static $_loader = null;

    /**
     * Instantiate a cache pattern
     *
     * @param string|Zend\Cache\Pattern\PatternInterface $name
     * @param array|Zend\Config $options
     * @return Zend\Cache\Pattern\PatternInterface
     * @throws Zend\Cache\LoaderException
     */
    public static function factory($name, $options = array())
    {
        if ($name instanceof PatternInterface) {
            Options::setOptions($name, $options);
            return $name;
        }
/*
        $loader = self::getLoader();

        try {
            $class = $loader->load($name);
        } catch (\Exception $e) {
            throw new LoaderException("Can't load cache pattern '{$name}'", 0, $e);
        }
*/

        $class = 'Zend\\Cache\\Pattern\\' . $name;

        return new $class($options);
    }

    /**
     * Get the pattern loader
     *
     * @return Zend\Loader\PluginLoader
     */
    public static function getLoader()
    {
        if (self::$_loader === null) {
            self::$_loader = self::_getDefaultLoader();
        }

        return self::$_loader;
    }

    /**
     * Set the pattern loader
     *
     * @param Zend\Loader\PluginLoader $loader
     * @return void
     */
    public static function setLoader(Loader $loader)
    {
        self::$_loader = $loader;
    }

    /**
     * Reset pattern loader to default
     *
     * @return void
     */
    public static function resetLoader()
    {
        self::$_loader = null;
    }

    /**
     * Get default pattern loader
     *
     * @return Zend\Loader\PluginLoader
     */
    protected static function _getDefaultLoader()
    {
        $loader = new Loader();
        // @todo: init $loader to load __DIR__ . '/Pattern/*' classes
        return $loader;
    }

}
