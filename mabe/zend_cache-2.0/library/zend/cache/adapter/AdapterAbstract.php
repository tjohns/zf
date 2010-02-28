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

namespace \zend\cache\adapter;
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
     * The last used key
     *
     * @var string|null
     */
    protected $_lastUsedKey = null;

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
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

    public function existMulti(array $keys, array $options = array())
    {
        $ret = array();
        foreach ($keys as $key) {
            if ($this->exist($key, $options)) {
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
            if ($this->_lastUsedKey === null) {
                throw new InvalidArgumentException('Missing key');
            }
        } else {
            $this->_lastUsedKey = $key;
        }

        return $this->_lastUsedKey;
    }

}
