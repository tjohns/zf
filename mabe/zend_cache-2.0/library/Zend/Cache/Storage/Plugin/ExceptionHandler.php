<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;
use \Zend\Cache\InvalidArgumentException;

/* HOWTO: use the exception handler to log exceptions:
$logger = new \Zend\Log\Logger();
$cache->setExceptionHandler(function (Exception $e) use ($logger) {
    $logger->err($e);
});
*/

class ExceptionHandler extends AbstractPlugin
{

    protected $_exceptionHandler = null;
    protected $_throwExceptions  = false;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (!$this->_exceptionHandler) {
            throw new InvalidArgumentException("Missing option 'exceptionHandler'");
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['exceptionHandler'] = $this->getExceptionHandler();
        $options['throwExceptions']  = $this->getThrowExceptions();
        return $options;
    }

    public function setExceptionHandler($callback)
    {
        if (!is_callable($callback, true)) {
            throw new InvalidArgumentException('The exception handler must be callable');
        }
        $this->_exceptionHandler = $callback;
    }

    public function getExceptionHandler()
    {
        return $this->_exceptionHandler;
    }

    public function setThrowExceptions($flag)
    {
        $this->_throwExceptions = (bool)$flag;
    }

    public function getThrowExceptions()
    {
        return $this->_throwExceptions;
    }

    public function set($value, $key = null, array $options = array())
    {
        try {
            return $this->getStorage()->set($value, $key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        try {
            return $this->getStorage()->setMulti($keyValuePairs, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function add($value, $key = null, array $options = array())
    {
        try {
            return $this->getStorage()->add($value, $key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        try {
            return $this->getStorage()->addMulti($keyValuePairs, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function replace($value, $key = null, array $options = array())
    {
        try {
            return $this->getStorage()->replace($value, $key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        try {
            return $this->getStorage()->replaceMulti($keyValuePairs, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function remove($key = null, array $options = array())
    {
        try {
            return $this->getStorage()->remove($key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function removeMulti(array $keys, array $options = array())
    {
        try {
            return $this->getStorage()->removeMulti($keys, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function get($key = null, array $options = array())
    {
        try {
            return $this->getStorage()->get($key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function getMulti(array $keys, array $options = array())
    {
        try {
            return $this->getStorage()->getMulti($keys, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function exists($key = null, array $options = array())
    {
        try {
            return $this->getStorage()->exists($key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function existsMulti(array $keys, array $options = array())
    {
        try {
            return $this->getStorage()->existsMulti($keys, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function info($key = null, array $options = array())
    {
        try {
            return $this->getStorage()->info($key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function infoMulti(array $keys, array $options = array())
    {
        try {
            return $this->getStorage()->infoMulti($keys, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        try {
            return $this->getStorage()->getDelayed($keys, $select, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        try {
            return $this->getStorage()->fetch($fetchStyle);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        try {
            return $this->getStorage()->fetchAll($fetchStyle);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function increment($value, $key = null, array $options = array())
    {
        try {
            return $this->getStorage()->increment($value, $key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        try {
            return $this->getStorage()->incrementMulti($keyValuePairs, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function decrement($value, $key = null, array $options = array())
    {
        try {
            return $this->getStorage()->decrement($value, $key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        try {
            return $this->getStorage()->decrementMulti($keyValuePairs, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function touch($key = null, array $options = array())
    {
        try {
            return $this->getStorage()->touch($key, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function touchMulti(array $keys, array $options = array())
    {
        try {
            return $this->getStorage()->touchMulti($keys, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function find($match = Storage::MATCH_ACTIVE, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        try {
            return $this->getStorage()->find($match, $select, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function clear($match = Storage::MATCH_EXPIRED, array $options = array())
    {
        try {
            return $this->getStorage()->clear($match, $options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function status(array $options)
    {
        try {
            return $this->getStorage()->status($options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function optimize(array $options = array())
    {
        try {
            return $this->getStorage()->optimize($options);
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

    public function lastKey()
    {
        try {
            return $this->getStorage()->lastKey();
        } catch (\Exception $e) {
            call_user_func($this->getExceptionHandler(), $e);
            if ($this->getThrowExceptions()) {
                throw $e;
            }

            return false;
        }
    }

}
