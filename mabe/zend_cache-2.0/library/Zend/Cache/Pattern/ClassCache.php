<?php

namespace Zend\Cache\Pattern;
use \Zend\Cache\RuntimeException;
use \Zend\Cache\InvalidArgumentException;
use \Zend\Cache\BadMethodCallException;

class ClassCache extends CallbackCache
{

    protected $_entity;
    protected $_cacheByDefault       = true;
    protected $_cacheMethods         = array();
    protected $_nonCacheMethods      = array();
    protected $_cacheMagicProperties = false;

    public function __construct($options)
    {
        parent::__construct($options);

        if (!$this->_entity) {
            throw InvalidArgumentException("Missing option 'entity'");
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['entity']               = $this->getEntity();
        $options['cacheByDefault']       = $this->getCacheByDefault();
        $options['cacheMethods']         = $this->getCacheMethods();
        $options['nonCacheMethods']      = $this->getNonCacheMethods();
        $options['cacheMagicProperties'] = $this->getCacheMagicProperties();
        return $options;
    }

    /**
     * Set the entity to cache
     *
     * @param string|object $entity The Classname for static calls or an object
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setEntity($entity)
    {
        if (!is_string($entity) && !is_object($entity)) {
            throw new InvalidArgumentException('Invalid entity, must be a class name or an object');
        }
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Get the entity to cache
     *
     * @return null|string|object The Classname for static calls or an object
     *                            or NULL if no entity was set
     */
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

            switch ($method) {
                case '__set':
                case '__get':
                case '__unset':
                case '__isset':
                    throw new InvalidArgumentException(
                        "Magic properties are handled by option 'cacheMagicProperties'"
                    );
            }
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

            switch ($method) {
                case '__set':
                case '__get':
                case '__unset':
                case '__isset':
                    throw new InvalidArgumentException(
                        "Magic properties are handled by option 'cacheMagicProperties'"
                    );
            }
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

    /**
     * Enable or disable caching of magic property calls
     *
     * @param boolean $flag
     * @return Zend\Cache\Pattern\ClassCache
     */
    public function setCacheMagicProperties($flag)
    {
        $this->_cacheMagicProperties = (bool)$flag;
        return $this;
    }

    /**
     * If caching of magic properties enabled
     *
     * @return boolean
     */
    public function getCacheMagicProperties()
    {
        return $this->_cacheMagicProperties;
    }

    /**
     * Call and cache a class method
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @param  array  $options Cache options
     * @return mixed
     * @throws Zend\Cache\Exception
     */
    public function call($method, array $args = array(), array $options = array())
    {
        $entity = $this->getEntity();
        $method = strtolower($method);

        // handle magic magic methods
        switch ($method) {
            case '__set':
                $property = array_shift($args);
                $value    = array_shift($args);

                if (is_object($entity)) {
                    $entity->{$property} = $value;
                } else {
                    // static property
                    $entity::$$property = $value;
                    return; // Property overloading only works in object context.
                }

                if ( !$this->getCacheMagicProperties()
                  || property_exists($entity, $property) ) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return;
                }

                // remove cached __get and __isset
                $removeKeys = null;
                if (method_exists($entity, '__get')) {
                    $removeKeys[] = $this->_generateKey(array($entity, '__get'), array($property));
                }
                if (method_exists($entity, '__isset')) {
                    $removeKeys[] = $this->_generateKey(array($entity, '__isset'), array($property));
                }
                if ($removeKeys) {
                    $this->getStorage()->removeMulti($removeKeys);
                }
                return;

            case '__get':
                $property = array_shift($args);

                if ( !$this->getCacheMagicProperties()
                  || !is_object($entity) // Property overloading only works in object context.
                  || property_exists($entity, $property)) {
                    if (is_object($entity)) {
                        return $entity->{$property};
                    } else {
                        // static property
                        return $entity::$$property;
                    }
                }

                array_unshift($args, $property);
                return parent::call(array($entity, '__get'), $args);

           case '__isset':
                $property = array_shift($args);

                if ( !$this->getCacheMagicProperties()
                  || !is_object($entity) // Property overloading only works in object context.
                  || property_exists($entity, $property)) {
                    if (is_object($entity)) {
                        return isset($entity->{$property});
                    } else {
                        // static property
                        return isset($entity::$$property);
                    }
                }

                return parent::call('__isset', array($property));

            case '__unset':
                $property = array_shift($args);

                if (is_object($entity)) {
                    unset($entity->{$name});
                } else {
                    // static property
                    unset($entity::$$name);
                    return; // Property overloading only works in object context.
                }

                if ( !$this->getCacheMagicProperties()
                  || property_exists($entity, $property)) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return;
                }

                // remove previous cached __get and __isset calls
                $removeKeys = null;
                if (method_exists($entity, '__get')) {
                    $removeKeys[] = $this->_generateKey(array($entity, '__get'), array($property));
                }
                if (method_exists($entity, '__isset')) {
                    $removeKeys[] = $this->_generateKey(array($entity, '__isset'), array($property));
                }
                if ($removeKeys) {
                    $this->getStorage()->removeMulti($removeKeys);
                }
                return;
        }

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($method, $this->getNonCacheMethods());
        } else {
            $cache = in_array($method, $this->getCacheMethods());
        }

        if (!$cache) {
            if ($args) {
                return call_user_func_array(array($entity, $method), $args);
            } else {
                return call_user_func(array($entity, $method));
            }
        }

        return parent::call(array($entity, $method), $args, $options);
    }

    /**
     * Remove a cached method call
     *
     * @param string $method
     * @param array $args
     * @param array $options
     */
    public function removeCall($method, array $args = array(), array $options = array())
    {
        return parent::removeCall(array($this->getEntity(), $method), $args, $options);
    }

    /**
     * Class method call handler
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @return mixed
     * @throws Zend\Cache\Exception
     */
    public function __call($method, array $args)
    {
        return $this->call($method, $args);
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
     * @param mixed  $value
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        return $this->call('__set', array($name, $value));
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
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        return $this->call('__get', array($name));
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
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __isset($name)
    {
        return $this->call('__isset', array($name));
    }

    /**
     * Unseting a property.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it removes
     * previous cached __isset and __get calls.
     *
     * @param string $name
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __unset($name)
    {
        return $this->call('__unset', array($name));
    }

    /**
     * Handle casting to string
     *
     * @return string
     * @see http://php.net/manual/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->call('__toString');
    }

    /**
     * Handle invoke calls
     *
     * @return mixed
     * @see http://php.net/manual/language.oop5.magic.php#language.oop5.magic.invoke
     */
    public function __invoke() {
        return $this->call('__invoke', func_get_args());
    }

}
