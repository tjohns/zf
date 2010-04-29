<?php

namespace Zend\Cache\Pattern;
use \Zend\Cache\RuntimeException;

class OutputCache extends AbstractPattern
{

    protected $_keyStack = array();

    /**
     * Start output cache
     *
     * @param  string  $key      Key
     * @param  array   $options  Output start options (ttl | validate | output)
     * @return mixed   True if the cache is hit or if output disabled the cached data, false else
     * @throws Zend_Cache_Exception
     */
    public function start($key, array $options = array())
    {
        $key = (string)$key;
        if (!isset($key[0])) { // strlen($key) == 0
            throw new InvalidArgumentException('Missing key');
        }

        $rs = $this->getStorage()->get($key, $options);
        if ($rs !== false) {
            if ( !isset($options['output']) || $options['output'] ) {
                echo $rs;
                return true;
            } else {
                return $rs;
            }
        }

        ob_start();
        ob_implicit_flush(false);
        $this->_keyStack[] = $key;
        return false;
    }

    /**
     * Stop output cache
     *
     * @param  array   $options   Ouput end options (ttl | priority | output | forcedata )
     * @throws Zend_Cache_Exception
     */
    public function end(array $options = array())
    {
        $key = array_pop($this->_keyStack);
        if ($key === null) {
            throw new RuntimeException('use of end() without a start()');
        }

        $data = ob_get_contents();
        ob_end_clean();

        $this->getStorage()->set($key, $data, $options);
        if (!isset($options['output']) || $options['output']) {
            echo $data;
        }
    }

}
