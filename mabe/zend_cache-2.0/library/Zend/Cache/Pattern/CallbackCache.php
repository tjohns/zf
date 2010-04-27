<?php

namespace Zend\Cache\Pattern;
use \Zend\Cache\InvalidArgumentException;

class CallbackCache extends AbstractPattern
{

    protected $_cacheByDefault    = true;
    protected $_cacheCallbacks    = array();
    protected $_nonCacheCallbacks = array();

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['cacheByDefault']    = $this->getCacheByDefault();
        $options['cacheCallbacks']    = $this->getCacheCallbacks();
        $options['nonCacheCallbacks'] = $this->getNonCacheCallbacks();
    }

    /**
     * Enable or disable caching of callbacks by default.
     *
     * @param boolean $flag
     * @return Zend\Cache\Pattern\CallbackCache
     */
    public function setCacheByDefault($flag)
    {
        $this->_cacheByDefault = (bool)$flag;
        return $this;
    }

    /**
     * Caching callbacks by default enabled.
     *
     * return boolean
     */
    public function getCacheByDefault()
    {
        return $this->_cacheByDefault;
    }

    /**
     * Enable cache callbacks
     *
     * @param callback[] $callbacks
     * @return Zend\Cache\Pattern\CallbackCache
     */
    public function setCacheCallbacks(array $callbacks)
    {
        foreach ($callbacks as &$callback) {
            if (!is_callable($callback, false, $callbackName)) {
                throw new InvalidArgumentException("Not a valid list of callbacks");
            }
            $callback = $callbackName;
        }
        $this->_cacheCallbacks = array_values(array_unique($callbacks));
    }

    /**
     * Get enabled cache callbacks
     *
     * @return string[] The named callbacks
     */
    public function getCacheCallbacks()
    {
        return $this->_cacheCallbacks;
    }

    /**
     * Disable cache callbacks
     *
     * @param callback[] $callbacks
     * @return Zend\Cache\Pattern\CallbackCache
     */
    public function setNonCacheCallbacks(array $callbacks)
    {
        foreach ($callbacks as &$callback) {
            if (!is_callable($callback, false, $callbackName)) {
                throw new InvalidArgumentException("Not a valid list of callbacks");
            }
            $callback = $callbackName;
        }
        $this->_nonCacheCallbacks = array_values(array_unique($callbacks));
    }

    /**
     * Get disabled cache callbacks
     *
     * @return string[] The named callbacks
     */
    public function getNonCacheCallbacks()
    {
        return $this->_nonCacheCallbacks;
    }

    /**
     * Main method : call the specified callback or get the result from cache
     *
     * @param  callback  $callback  A valid callback
     * @param  array     $args      Callback arguments
     * @param  array     $options   Options
     * @return mixed Result
     * @throws Zend\Cache\Exception
     */
    public function call($callback, array $args = array(), array $options = array())
    {
        if (!is_callable($callback, false, $callbackName)) {
            throw new InvalidArgumentException('Invalid callback given');
        }

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($callbackName, $this->getNonCacheCallbacks());
        } else {
            $cache = in_array($callbackName, $this->getCacheCallbacks());
        }

        if ( !$cache ) {
            // do not cache
            return call_user_func_array($callback, $args);
        }

        $key = $this->_makeKey($callbackName, $args);
        if ( ($rs = $this->getStorage()->get($key, $options)) !== false ) {
            if (!isset($rs[0]) || !array_key_exists(1, $rs)) {
                throw new RuntimeException("Invalid cached data for key '{$key}'");
            }

            echo $rs[0];
            return $rs[1];
        }

        // cache doesn't hit
        ob_start();
        ob_implicit_flush(false);
        $ret = call_user_func_array($callback, $args);
        $out = ob_get_flush();
        $this->getStorage()->set($key, array($out, $ret), $options);

        return $ret;
    }

    /**
     * Make a key from the callback name and arguments
     *
     * @param  string $name  Callback name
     * @param  array  $args  Callback arguments
     * @return string
     * @throws Zend\Cache\Exception
     */
    protected function _makeKey($name, array $args)
    {
        $argsStr = '';
        if (count($args) > 0) {
            $argsStr = @serialize(array_values($args));
            if (!$argsStr) {
                $lastErr = error_get_last();
                throw new RuntimeException("Can't serialize arguments to generate key: {$lastErr['message']}");
            }
        }

        return md5($name . $argsStr);
    }

}
