<?php

namespace Zend\Cache\Pattern;
use \Zend\Cache\RuntimeException;
use \Zend\Cache\InvalidArgumentException;

class CallbackCache extends AbstractPattern
{

    /**
     * Call the specified callback or get the result from cache
     *
     * @param  callback   $callback  A valid callback
     * @param  array      $args      Callback arguments
     * @param  array      $options   Options
     * @return mixed Result
     * @throws Zend\Cache\Exception
     */
    public function call($callback, array $args = array(), array $options = array())
    {
        $key = $this->_makeKey($callbackName, $args);
        if ( ($rs = $this->getStorage()->get($key, $options)) !== false ) {
            if (!isset($rs[0], $rs[1])) {
                throw new RuntimeException("Invalid cached data for key '{$key}'");
            }

            echo $rs[0];
            return $rs[1];
        }

        // cache doesn't hit
        ob_start();
        ob_implicit_flush(false);

        if ($args) {
            $ret = call_user_func_array($callback, $args);
        } else {
            $ret = call_user_func($callback);
        }

        $out = ob_get_flush();
        $this->getStorage()->set(array($out, $ret), $key, $options);

        return $ret;
    }

    /**
     * Remove a cached callback call
     *
     * @param  callback   $callback  A valid callback
     * @param  array      $args      Callback arguments
     * @param  array      $options   Options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function removeCall($callback, array $args = array(), array $options = array())
    {
        $key = $this->_makeKey($callbackName, $args);
        return $this->getStorage()->remove($key, $options);
    }

    /**
     * Make a key from the callback name and arguments
     *
     * @param  callback   $callback  A valid callback
     * @param  array      $args      Callback arguments
     * @return string
     * @throws Zend\Cache\Exception
     */
    protected function _makeKey($callback, array $args = array())
    {
        if (!is_callable($callback, true, $name)) {
            throw new InvalidArgumentException('Invalid callback given');
        }

        $argsStr = '';
        if ($args) {
            $argsStr = @serialize(array_values($args));
            if (!$argsStr) {
                $lastErr = error_get_last();
                throw new RuntimeException("Can't serialize arguments to generate key: {$lastErr['message']}");
            }
        }

        return md5($name . $argsStr);
    }

}
