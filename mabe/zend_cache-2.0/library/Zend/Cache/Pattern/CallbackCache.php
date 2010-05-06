<?php

namespace Zend\Cache\Pattern;
use \Zend\Cache\RuntimeException;
use \Zend\Cache\InvalidArgumentException;

class CallbackCache extends AbstractPattern
{

    /**
     * Main method : call the specified callback or get the result from cache
     *
     * @param  callback   $callback  A valid callback
     * @param  array|null $args      Callback arguments or NULL for no arguments
     * @param  array      $options   Options
     * @return mixed Result
     * @throws Zend\Cache\Exception
     */
    public function call($callback, array $args = null, array $options = array())
    {
        if (!is_callable($callback, false, $callbackName)) {
            throw new InvalidArgumentException('Invalid callback given');
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

        if ($args !== null) {
            $ret = call_user_func_array($callback, $args);
        } else {
            $ret = call_user_func($callback);
        }

        $out = ob_get_flush();
        $this->getStorage()->set(array($out, $ret), $key, $options);

        return $ret;
    }

    /**
     * Make a key from the callback name and arguments
     *
     * @param  string     $name  Callback name
     * @param  null|array $args  Callback arguments
     * @return string
     * @throws Zend\Cache\Exception
     */
    protected function _makeKey($name, array $args = null)
    {
        $argsStr = '';
        if ($args !== null && count($args) > 0) {
            $argsStr = @serialize(array_values($args));
            if (!$argsStr) {
                $lastErr = error_get_last();
                throw new RuntimeException("Can't serialize arguments to generate key: {$lastErr['message']}");
            }
        }

        return md5($name . $argsStr);
    }

}
