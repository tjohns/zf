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

/**
 * Memory manager
 *
 * This class encapsulates memory menagement operations, when PHP works
 * in limited memory mode.
 *
 *
 * @category   Zend
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Memory_Manager
{
    /**
     * Object storage backend
     *
     * @var Zend_Cache_Backend_Interface
     */
    private $_backend;

    /**
     * Memory grow limit.
     * Default value is 2/3 of memory_limit php.ini variable
     * Negative value means no limit
     *
     * @var integer
     */
    private $_memoryLimit;

    /**
     * Minimum value size to be swapped.
     * Default value is 16K
     * Negative value means that all values may be swapped
     *
     * @var integer
     */
    private $_minSize;

    /**
     * Overall size of memory, used by values
     *
     * @var integer
     */
    private $_memorySize;

    /**
     * Id for next Zend_Memory object
     *
     * @var integer
     */
    private $_nextId;

    /**
     * List of Zend_Memory objects
     *
     * @var array
     */
    private $_objects;


    /**
     * Zend_Memory objects access history
     *
     * @var array
     */
    private $_accessHistory;


    /**
     * Dinamic list of loaded objects
     *
     * @var array
     */
    private $_loadedObjects;


    /**
     * Memory manager constructor
     *
     * If backend is not specified, then 'File' backend with default options is used
     *
     * @param Zend_Cache_Backend $backend
     * @param array $backendOptions associative array of options for the corresponding backend constructor
     */
    public function __construct(Zend_Cache_Backend $backend)
    {
        $this->_backend = $backend;

        $memoryLimitStr = trim(ini_get('memory_limit'));
        if ($memoryLimitStr != '') {
            $this->_memoryLimit = (integer)$memoryLimitStr;
            switch (strtolower($memoryLimitStr[strlen($memoryLimitStr)-1])) {
                case 'g':
                    $this->_memoryLimit *= 1024;
                    // Break intentionally omitted
                case 'm':
                    $this->_memoryLimit *= 1024;
                    // Break intentionally omitted
                case 'k':
                    $this->_memoryLimit *= 1024;
                    break;

                default:
                    break;
            }
        } else {
            $this->_memoryLimit = -1;  // No limit
        }

        $this->_minSize    = 16384;
        $this->_memorySize = 0;
        $this->_nextId     = 0;
        $this->_objects       = array();
        $this->_accessHistory = array();
    }

    /**
     * Set memory grow limit
     *
     * @param integer $newLimit
     * @throws Zend_Exception
     */
    public function setMemoryLimit($newLimit)
    {
        $this->_memoryLimit = $newLimit;

        if ($this->_memoryLimit > 0  &&  $this->_memoryLimit < $this->_memorySize) {
            /** @todo swap some objects */
            throw new Zend_Memory_Exception('Unimplemented');
        }
    }

    /**
     * Get memory grow limit
     *
     * @return integer
     */
    public function getMemoryLimit()
    {
        return $this->_memoryLimit;
    }

    /**
     * Set minimum size of values, which may be swapped
     *
     * @param integer $newSize
     */
    public function setMinSize($newSize)
    {
        $this->_minSize = $newSize;
    }

    /**
     * minimum size of values, which may be swapped
     *
     * @return integer
     */
    public function getMinSize()
    {
        return $this->_minSize;
    }

    /**
     * Create new Zend_Memory object
     *
     * @param string $value
     * @return Zend_Memory_Container
     * @throws Zend_Memory_Exception
     */
    public function create($value = '')
    {
        $id = $this->_nextId++;

        $valueObject         = new Zend_Memory_Container($this, $id, $value);
        $this->_objects[$id] = $valueObject;

        // Put object id on a top of access history
        $this->_accessHistory[$id] = $id;

        return $valueObject;
    }

    /**
     * Get Zend_Memory object
     *
     * @param integer $id
     * @return Zend_Memory_Container
     */
    public function get($id)
    {
        return $this->_objects[$id];
    }

    /**
     * Swap object data to disk
     * Actualy swaps data or only unloads it from memory,
     * if object is not changed since last swap
     */
    private function _swap($id)
    {
        /** @todo implementation */
    }

    /**
     * Load value from swap file.
     */
    private function _load($id)
    {
        /** @todo implementation */
    }
}
