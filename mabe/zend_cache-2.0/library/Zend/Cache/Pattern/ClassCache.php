<?php

namespace Zend\Cache\Pattern;

class ClassCache extends CallbackCache
{

    protected $_entity;
    protected $_cacheByDefault       = true;
    protected $_cacheMethods         = array();
    protected $_nonCacheMethods      = array();
    protected $_cacheMagicProperties = false;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['entity'] = $this->getEntity();
        $options['cacheByDefault']       = $this->getCacheByDefault();
        $options['cacheMethods']         = $this->getCacheMethods();
        $options['nonCacheMethods']      = $this->getNonCacheMethods();
        $options['cacheMagicProperties'] = $this->getCacheMagicProperties();
        return $options;
    }

    public function setEntity($entity)
    {
        if (!is_string($entity) && !is_object($entity)) {
            throw new InvalidArgumentException('Invalid entity, must be a class name or an object');
        }
        $this->_entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Enable or disable caching of methods by default.
     *
     * @param boolean $flag
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setCacheByDefault($flag)
    {
        $this->_cacheByDefault = (bool)$flag;
        return $this;
    }

    /**
     * Caching methods by default enabled.
     *
     * return boolean
     */
    public function getCacheByDefault()
    {
        return $this->_cacheByDefault;
    }

    /**
     * Enable cache methods
     *
     * @param string[] $methods
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setCacheMethods(array $methods)
    {
        foreach ($methods as &$method) {
            $method = strtolower($method);
        }
        $this->_cacheMethods = array_values(array_unique($methods));
    }

    /**
     * Get enabled cache methods
     *
     * @return string[]
     */
    public function getCacheMethods()
    {
        return $this->_cacheMethods;
    }

    /**
     * Disable cache methods
     *
     * @param string[] $methods
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setNonCacheMethods(array $methods)
    {
        foreach ($methods as &$method) {
            $method = strtolower($method);
        }

        $this->_nonCacheMethods = array_values(array_unique($methods));
    }

    /**
     * Get disabled cache methods
     *
     * @return string[]
     */
    public function getNonCacheMethods()
    {
        return $this->_nonCacheMethods;
    }

    public function setCacheMagicProperties($flag)
    {
        $this->_cacheMagicProperties = (bool)$flag;
        return $this;
    }

    public function getCacheMagicProperties()
    {
        return $this->_cacheMagicProperties;
    }

    /**
     * Class method call handler
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @return mixed
     * @throws RuntimeException
     */
    public function __call($method, array $args)
    {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        $method = strtolower($method);

        // handle magic magic methods
        switch ($method) {
            case '__set':      return $this->__set(array_shift($args), array_shift($args));
            case '__get':      return $this->__get(array_shift($args), array_shift($args));
            case '__isset':    return $this->__isset(array_shift($args), array_shift($args));
            case '__unset':    return $this->__unset(array_shift($args), array_shift($args));
            case '__tostring': return $this->__toString();
            case '__invoke':   return call_user_func_array(array($this, '__invoke', $args));
        }

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($method, $this->getNonCacheMethods());
        } else {
            $cache = in_array($method, $this->getCacheMethods());
        }

        if (!$cache) {
            return call_user_func_array(array($entity, $method), $args);
        }

        return $this->call(array($entity, $method), $args);
    }

    /**
     * Writing data to properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __set
     * and removes cached data of previous __get and __isset calls.
     *
     * @param string $name
     * @param array $value
     */
    public function __set($name, $value)
    {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        if (is_object($entity)) {
            $entity->{$name} = $value;
        } else {
            // static property
            $entity::$$name = $value;
            return;
        }

        if ( !$this->getCacheMagicProperties()
          || property_exists($entity, $name)) {
            // no caching if property isn't magic
            // or caching magic properties is disabled
            return;
        }

        // remove cached __get and __isset
        $removeKeys = null;
        if (is_callable(array($entity, '__get'), false, $callbackName)) {
            $removeKeys[] = $this->_makeKey($callbackName, array($name));
        }
        if (is_callable(array($entity, '__isset'), false, $callbackName)) {
            $removeKeys[] = $this->_makeKey($callbackName, array($name));
        }
        if ($removeKeys) {
            $this->getStorage()->removeMulti($removeKeys);
        }
    }

    /**
     * Reading data from properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __get.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        if ( !$this->getCacheMagicProperties()
          || !is_object($entity)
          || property_exists($entity, $name)) {
            if (is_object($entity)) {
                return $entity->{$name};
            } else {
                // static property
                return $entity::$$name;
            }
        }

        return $this->call(array($entity, '__get'), $args);
    }

    /**
     * Checking existing properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __get.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        if ( !$this->getCacheMagicProperties()
          || !is_object($entity)
          || property_exists($entity, $name)) {
            if (is_object($entity)) {
                return isset($entity->{$name});
            } else {
                // static property
                return isset($entity::$$name);
            }
        }

        return $this->call('__isset', array($name));
    }

    /**
     * Unseting a propertie.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it removes
     * previous cached __isset and __get calls.
     *
     * @param string $name
     */
    public function __unset($name)
    {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        if (is_object($entity)) {
            unset($entity->{$name});
        } else {
            // static property
            unset($entity::$$name);
            return;
        }

        if ( !$this->getCacheMagicProperties()
          || property_exists($entity, $name)) {
            // no caching if property isn't magic
            // or caching magic properties is disabled
            return;
        }

        // remove previous cached __set, __get and __isset calls
        $removeKeys = array();
        if (is_callable(array($entity, '__get'), false, $callbackName)) {
            $removeKeys[] = $this->_makeId($callbackName, array($name));
        }
        if (is_callable(array($entity, '__isset'), false, $callbackName)) {
            $removeKeys[] = $this->_makeId($callbackName, array($name));
        }
        if ($removeKeys) {
            $this->getStorage()->removeMulti($removeKeys);
        }
    }

    /**
     * Converts cache entity to string
     *
     * @return string
     */
    public function __toString()
    {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        if (!is_object($entity)) {
            throw new BadMethodCallException(
                "The static method '__toString' isn't allowed"
            );
        }

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array('__tostring', $this->getNonCacheMethods());
        } else {
            $cache = in_array('__tostring', $this->getCacheMethods());
        }

        if (!$cache) {
            return $entity->{'__toString'}();
        }

        return $this->call('__toString', $args);
    }

    /**
     * Handle invoke calls
     *
     * @return mixed
     * @see http://php.net/manual/language.oop5.magic.php#language.oop5.magic.invoke
     */
    public function __invoke() {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array('__invoke', $this->getNonCacheMethods());
        } else {
            $cache = in_array('__invoke', $this->getCacheMethods());
        }

        if (!$cache) {
            return call_user_func_array(array($entity, '__invoke'), func_get_args());
        }

        return $this->call('__invoke', func_get_args());
    }

}
