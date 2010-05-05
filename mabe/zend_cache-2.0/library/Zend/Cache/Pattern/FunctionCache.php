<?php

namespace Zend\Cache\Pattern;

class FunctionCache extends CallbackCache
{

    protected $_cacheByDefault    = true;
    protected $_cacheFunctions    = array();
    protected $_nonCacheFunctions = array();

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['cacheByDefault']    = $this->getCacheByDefault();
        $options['cacheFunctions']    = $this->getCacheMethods();
        $options['nonCacheFunctions'] = $this->getNonCacheMethods();
        return $options;
    }

    /**
     * Enable or disable caching by default
     *
     * @param boolean $flag
     * @return Zend\Cache\Pattern\FunctionCache
     */
    public function setCacheByDefault($flag)
    {
        $this->_cacheByDefault = (bool)$flag;
        return $this;
    }

    /**
     * Caching by default enabled
     *
     * @return boolean
     */
    public function getCacheByDefault()
    {
        return $this->_cacheByDefault;
    }

    /**
     * Enable caching on functions
     *
     * @param string[] $functions
     * @return Zend\Cache\Pattern\FunctionCache
     */
    public function setCacheFunctions(array $functions)
    {
        foreach ($functions as &$function) {
            $function = strtolower($function);
        }

        $this->_cacheFunctions = array_values(array_unique($functions));
        return $this;
    }

    /**
     * Get cachable functions
     *
     * @return string[]
     */
    public function getCacheFunctions()
    {
        return $this->_cacheFunctions;
    }

    /**
     * Disable caching on functions
     *
     * @param string[] $functions
     * @return Zend\Cache\Pattern\FunctionCache
     */
    public function setNonCacheFunctions(array $functions)
    {
        foreach ($functions as &$function) {
            $function = strtolower($function);
        }

        $this->_nonCacheFunctions = array_values(array_unique($functions));
        return $this;
    }

    /**
     * Get functions disabled for caching
     *
     * @return string[]
     */
    public function getNonCacheFunctions()
    {
        return $this->_nonCacheFunctions;
    }

    /**
     * function call handler
     *
     * @param  string $function  Function name to call
     * @param  array  $args      Function arguments
     * @return mixed
     * @throws Zend_Cache_Exception
     */
    public function __call($function, array $args = array())
    {
        $function = strtolower($function);

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($function, $this->getNonCacheFunctions());
        } else {
            $cache = in_array($function, $this->getCacheFunctions());
        }

        if (!$cache) {
            return call_user_func_array($function, $args);
        }

        return $this->call($function, $args);
    }

}
