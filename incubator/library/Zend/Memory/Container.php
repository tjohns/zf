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
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Memory_Exception */
require_once 'Zend/Memory/Exception.php';

/** Zend_Memory_Container_Interface */
require_once 'Zend/Memory/Container/Interface.php';

/** Zend_Memory_Value */
require_once 'Zend/Memory/Value.php';


/**
 * Memory value container
 *
 * @category   Zend
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Memory_Container implements Zend_Memory_Container_Interface
{
    /**
     * Internal object Id
     *
     * @var integer
     */
    private $_id;

    /**
     * Value object
     *
     * @var Zend_Memory_Value
     */
    private $_value;

    /**
     * Value size
     *
     * @var integer
     */
    private $_size;

    /**
     * Memory manager reference
     *
     * @var Zend_Memory_Manager
     */
    private $_memManager;

    /** Value states */
    const LOADED   = 1;
    const SWAPPED  = 2;
    const LOCKED   = 4;

    /**
     * Value state (LOADED/SWAPPED)
     *
     * @var integer
     */
    private $_state;

    public function __construct($memoryManager, $id, $value)
    {
        $this->_memManager = $memoryManager;
        $this->_id    = $id;
        $this->_size  = strlen($value);
        $this->_state = self::LOADED;
        $this->_value = new Zend_Memory_Value($value, $this);
    }

    public function __destruct()
    {
        if ($this->_state & self::SWAPPED) {
            /** @todo Clear swap */
        }
    }

    /**
     * Lock object in memory.
     * If writeLock is true, than object is locked for writing
     * Otherwise only read lock is obtained.
     * (Write lock also discards swapped data)
     *
     * @param boolean $writeLock
     */
    public function lock($writeLock = true)
    {
        if ( !($this->_state & self::LOADED) ) {
            /** @todo Load value from a swap */

            $this->_state |= self::LOADED;
        }

        if ($writeLock  && ($this->_state & self::SWAPPED)) {
            /** @todo Clear swap */

            $this->_state &= ~self::SWAPPED;
        }

        $this->_state |= self::LOCKED;
    }

    /**
     * Unlock object
     */
    public function unlock()
    {
        $this->_state &= ~self::LOCKED;
    }

    /**
     * Return true if object is locked
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->_state & self::LOCKED;
    }

    /**
     * Get memory object id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get handler
     *
     * Loads object if necessary and moves it to the top of loaded objects list.
     * Swaps objects from the bottom of loaded objects list, if necessary.
     *
     * @param string $property
     * @return string
     * @throws Zend_Memory_Exception
     */
    public function __get($property)
    {
        if ($property != 'value') {
            throw new Zend_Memory_Exception('Unknown property: Zend_Memory_container::$' . $property);
        }

        if ( !($this->_state & self::LOADED) ) {
            /** @todo Load value from a swap */

            $this->_state |= self::LOADED;
        }

        return $this->_value;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  string $value
     * @throws Zend_Exception
     */
    public function __set($property, $value)
    {
        if ($property != 'value') {
            throw new Zend_Memory_Exception('Unknown property: Zend_Memory_container::$' . $property);
        }

        if ($this->_state & self::SWAPPED) {
            /** @todo Clean up swap */

            $this->_state &= ~self::SWAPPED;
        }

        $this->_size  = strlen($value);
        $this->_state |= ~self::LOADED;
        $this->_value = new Zend_Memory_Value($value, $this);
    }


    /**
     * Get string value reference
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @return string
     */
    public function &getRef()
    {
        return $this->_value->getRef();
    }

    /**
     * Signal, that value is updated by external code.
     *
     * Should be used together with getRef()
     */
    public function touch()
    {
        if ($this->_state == self::STORED) {
            $this->_state = self::MODIFIED;
            $this->_container->processUpdate();
        }
    }

    /**
     * Zend_Memory_Container interface
     *
     * Process container value update.
     * Must be called only by value object
     */
    public function processUpdate()
    {
        if ($this->_state & self::SWAPPED) {
            /** @todo Clear swap */

            $this->_state &= ~self::SWAPPED;
        }

        $this->_size = strlen($this->_value->getRef());
    }
}
