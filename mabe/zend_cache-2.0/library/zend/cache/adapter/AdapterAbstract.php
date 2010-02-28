<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace zend\cache\adapter;
use \zend\Options as Options;
use \zend\cache\InvalidArgumentException as InvalidArgumentException;

/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AdapterAbstract implements AdapterInterface
{

    /**
     * TTL option
     *
     * @var int 0 means infinite or maximum of adapter
     */
    protected $_ttl = 0;

    /**
     * The last used key
     *
     * @var string|null
     */
    protected $_lastKey = null;

    /**
     * The fetchBuffer for getDelayed calls if backend doen't support this.
     *
     * @var array|null
     */
    protected $_fetchBuffer = array();

    public function __construct($options = array())
    {
        Options::setConstructorOptions($this, $options);
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
    }

    public function setTtl($ttl)
    {
        $this->_ttl = $this->_ttl($ttl);
    }

    public function getTtl()
    {
        return $this->_ttl;
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->set($value, $key, $options) && $ret;;
        }
        return $ret;
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->add($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($idDataList as $key => $value) {
            $ret = $this->replace($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function getMulti(array $keys, array $options = array())
    {
        $ret = array();
        foreach ($keys as $key) {
            if ( ($value = $this->get($key, $options)) ) {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    public function existsMulti(array $keys, array $options = array())
    {
        $ret = array();
        foreach ($keys as $key) {
            if ($this->exists($key, $options)) {
                $ret[] = $key;
            }
        }
        return $ret;
    }

    public function infoMulti(array $keys, array $options = array())
    {
        $ret = array();
        foreach ($keys as $key) {
            if ( ($info = $this->info($key, $options)) ) {
                $ret[$key] = $info;
            }
        }
        return $ret;
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $ret = true;
        foreach ($keys as $key) {
            $ret = $this->remove($key) && $ret;
        }
        return $ret;
    }

    public function getDelayed(array $keys, array $options = array()) {
        if (isset($opts['callback'])) {
            $cb = $opts['callback'];
            if (!is_callable($cb, false)) {
                throw new Zend_Cache_Exception('Invalid callback');
            }
            foreach ($this->getMulti($keys, $options) as $key => $value) {
                $cb($key, $value);
            }
        } else {
            $this->_fetchBuffer = $this->getMulti($keys, $options);
        }
    }

    public function fetch() {
        if (!$this->_fetchBuffer) {
            return false;
        }

        // array_shift can't use because its modify all numeric array keys
        $k = key($this->_fetchBuffer);
        $v = current($this->_fetchBuffer);
        unset($this->_fetchBuffer[$k]);
        return array($k => $v);
    }

    public function fetchAll() {
        $ret = $this->_fetchBuffer;
        $this->_fetchBuffer = array(); // free memory
        return $ret;
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->increment($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->decrement($value, $key, $options) && $ret;
        }
        return $ret;
    }

    public function optimize(array $options = array())
    {
        return true;
    }

    public function lastKey()
    {
        return $this->_lastKey;
    }

    protected function _ttl($ttl)
    {
        $ttl = (int)$ttl;
        if ($ttl < 0) {
             throw new InvalidArgumentException('The ttl can\'t be negative');
        }
        return $ttl;
    }

    /**
     * Get a normalized key.
     * - If key is empty get the last used key.
     *
     * @param string|null $key
     * @return string
     */
    protected function _key($key)
    {
        if ( ($key = (string)$key) === '') {
            if ($this->_lastKey === null) {
                throw new InvalidArgumentException('Missing key');
            }
        } else {
            $this->_lastKey = $key;
        }

        return $this->_lastKey;
    }

}
