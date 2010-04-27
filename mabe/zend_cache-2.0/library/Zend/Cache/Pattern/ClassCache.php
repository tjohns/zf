<?php

namespace Zend\Cache\Pattern;

class ClassCache extends CallbackCache
{

    protected $_entity;
    protected $_cacheMagicProperties = false;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['entity'] = $this->getEntity();
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

        if ( !$this->getCacheMagicProperties()
          || !is_object($entity)
          || property_exists($entity, $name)) {
            if (is_object($entity)) {
                $entity->{$name} = $value;
            } else {
                $entity::$$name = $value;
            }
            return;
        }

        $this->call(array($entity, '__set'), array($name, $value));

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
                return $entity::$$name;
            }
        }

        return $this->call(array($entity, '__get'), array($name));
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
                return isset($entity::$$name);
            }
        }

        return $this->call(array($entity, '__isset'), array($name));
    }

    /**
     * Unseting properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it removes
     * previous cached __isset, __get and __set calls.
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
            unset($entity::$$name);
        }

        if ( !$this->getCacheMagicProperties()
          || !is_object($entity)
          || property_exists($entity, $name)) {
            return;
        }

        // remove previous cached __set, __get and __isset calls
        $removeKeys = array();
        if (is_callable(array($entity, '__set'), false, $callbackName)) {
            $removeKeys[] = $this->_makeId($callbackName, array($name));
        }
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
     * NOTE:
     * If cache entity doesn't implement __toString than a simple type cast is
     * done without caching else __toString will be called with normal cache
     * handling.
     *
     * @return string
     */
    public function __toString()
    {
        $entity = $this->getEntity();
        if (!$entity) {
            throw new RuntimeException('Missing entity');
        }

        if ( !is_object($entity)
          || !method_exists($entity, '__toString')) {
            // Don't cache if __toString doesn't exists and use simple type casting
            return (string)$entity;
        }

        return $this->call(array($entity, '__toString'), $args);
    }

    /**
     * Handle invoke calls
     *
     * @return mixed
     * @see http://php.net/manual/language.oop5.magic.php#language.oop5.magic.invoke
     */
    public function __invoke() {
        return $this->__call('__invoke', func_get_args());
    }

}
