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
        $key = $this->generateKey($callback, $args);
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

        try {
            if ($args) {
                $ret = call_user_func_array($callback, $args);
            } else {
                $ret = call_user_func($callback);
            }
        } catch (Exception $e) {
            ob_end_flush();
            throw $e;
        }

        $out = ob_get_flush();
        $this->getStorage()->set(array($out, $ret), $key, $options);

        return $ret;
    }

    /**
     * Generate a key from the callback and arguments
     *
     * @param  callback   $callback  A valid callback
     * @param  array      $args      Callback arguments
     * @return string
     * @throws Zend\Cache\Exception
     */
    public function generateKey($callback, array $args = array())
    {
        return $this->_generateKey($callback, $args);
    }

    /**
     * Generate a key from the callback and arguments
     *
     * @param  callback   $callback  A valid callback
     * @param  array      $args      Callback arguments
     * @return string
     * @throws Zend\Cache\Exception
     */
    public function _generateKey($callback, array $args = array())
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
