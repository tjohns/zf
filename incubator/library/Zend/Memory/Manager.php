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

/** Zend_Memory_Container_Movable */
require_once 'Zend/Memory/Container/Movable.php';

/** Zend_Memory_Container_Locked */
require_once 'Zend/Memory/Container/Locked.php';

/** Zend_Memory_AccessController */
require_once 'Zend/Memory/AccessController.php';


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
class Zend_Memory_Manager
{
    /**
     * Object storage backend
     *
     * @var Zend_Cache_Backend_Interface
     */
    private $_backend = null;

    /**
     * Memory grow limit.
     * Default value is 2/3 of memory_limit php.ini variable
     * Negative value means no limit
     *
     * @var integer
     */
    private $_memoryLimit = -1;

    /**
     * Minimum value size to be swapped.
     * Default value is 16K
     * Negative value means that memory objects are never swapped
     *
     * @var integer
     */
    private $_minSize = -1;

    /**
     * Overall size of memory, used by values
     *
     * @var integer
     */
    private $_memorySize = 0;

    /**
     * Id for next Zend_Memory object
     *
     * @var integer
     */
    private $_nextId = 0;

    /**
     * Dinamic list of loaded objects
     *
     * It also represents objects access history. Last accessed objects are moved to the end of array
     *
     * array(
     *     <id> => <memory container object>,
     *     ...
     *      )
     *
     * @var array
     */
    private $_loadedObjects = array();

    /**
     * List of object sizes.
     *
     * This list is used to calculate modification of object sizes
     *
     * array( <id> => <size>, ...)
     *
     * @var array
     */
    private $_sizes = array();

    /**
     * Last modified object
     *
     * It's used to reduce number of calls necessary to trace objects' modifications
     * Modification is not processed by memory manager until we do not switch to another
     * object.
     * So we have to trace only _first_ object modification and do nothing for others
     *
     * @var Zend_Memory_Container_Movable
     */
    private $_lastModified = null;


    /**
     * Memory manager constructor
     *
     * If backend is not specified, then memory objects are never swapped
     *
     * @param Zend_Cache_Backend $backend
     * @param array $backendOptions associative array of options for the corresponding backend constructor
     */
    public function __construct($backend = null)
    {
        if ($backend === null) {
            return;
        }

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
        } // No limit otherwise

        $this->_minSize    = 16384;
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

        $this->_swapCheck();
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
     * Get minimum size of values, which may be swapped
     *
     * @return integer
     */
    public function getMinSize()
    {
        return $this->_minSize;
    }

    /**
     * Create new Zend_Memory value container
     *
     * @param string $value
     * @return Zend_Memory_Container_Interface
     * @throws Zend_Memory_Exception
     */
    public function create($value = '')
    {
        return $this->_create($value,  false);
    }

    /**
     * Create new Zend_Memory value container, which has value always
     * locked in memory
     *
     * @param string $value
     * @return Zend_Memory_Container_Interface
     * @throws Zend_Memory_Exception
     */
    public function createLocked($value = '')
    {
        return $this->_create($value, true);
    }

    /**
     * Create new Zend_Memory object
     *
     * @param string $value
     * @param boolean $locked
     * @return Zend_Memory_Container_Interface
     * @throws Zend_Memory_Exception
     */
    private function _create($value, $locked)
    {
        $id = $this->_nextId++;

        if ($locked  ||  ($this->_backend === null) /* Use only memory locked objects if backend is not specified */) {
            return new Zend_Memory_Container_Locked($value);
        }

        // Commit modifications
        $this->_commit();

        $valueObject = new Zend_Memory_Container_Movable($this, $id, $value);

        // Store last object size as 0
        $this->_sizes[$id] = 0;
        // prepare object for next modifications
        $this->_lastModified = $valueObject;

        return new Zend_Memory_AccessController($valueObject);
    }

    /**
     * Unlink value container from memory manager
     *
     * Used by Memory container destroy() method
     *
     * @internal
     * @param integer $id
     * @return Zend_Memory_Container
     */
    public function unlink(Zend_Memory_Container_Movable $container, $id)
    {
        if ($this->_lastModified === $container) {
            // Drop all object modifications and do nothing
            $this->_lastModified = null;
        }

        if (isset($this->_loadedObjects[$id])) {
            $this->_memorySize -= $this->_sizes[$id];
            unset($this->_loadedObjects[$id]);
        }

        unset($this->_sizes[$id]);
    }

    /**
     * Process value update
     *
     * @internal
     * @param Zend_Memory_Container_Movable $container
     * @param integer $id
     */
    public function processUpdate(Zend_Memory_Container_Movable $container, $id)
    {
        /**
         * This method is automatically invoked by memory container only once per
         * "modification session", but user may call memory container touch() method
         * several times depending on used algorithm. So we have to use this check
         * to optimize this case.
         */
        if ($container === $this->_lastModified) {
            return;
        }

        $this->_commit();

        //
        $this->_lastModified = $container;
    }

    /**
     * Commit modified object and put it back to the loaded objects list
     */
    private function _commit()
    {
        $container = $this->_lastModified;

        if ($container === null) {
            return;
        }

        $this->_lastModified = null;

        $id = $container->getId();

        $this->_loadedObjects[$id] = $container;
        $container->startTrace();

        $this->_memorySize += $this->_sizes[$id];

    }

    /**
     * Check and swap objects if necessary
     *
     * @throws Zend_MemoryException
     */
    private function _swapCheck()
    {
        if ($this->_memoryLimit < 0  ||  $this->_memorySize < $this->_memoryLimit) {
            // Memory limit is not reached
            // Do nothing
            return;
        }

        $swapSizeLimit = $this->_minSize;

        // walk through loaded objects in access history order
        foreach ($this->_loadedObjects as $id => $container) {
            if ($this->_sizes[$id] > $swapSizeLimit) {
                $this->_swap($container, $id);

                if ($this->_memorySize < $this->_memoryLimit) {
                    // We've swapped enough objects
                    return;
                }
            }
        }

        throw new Zend_Memory_Exception("Memory manager can't get enough space. \n"
                                      . "Memory limit: " . $this->_memoryLimit . ". \n"
                                      . "Number of non-swappable objects: " . count($this->_loadedObjects) . ". \n"
                                      . "Minimum size of swappable objects: " . $this->_minSize . ". ");
    }


    /**
     * Swap object data to disk
     * Actualy swaps data or only unloads it from memory,
     * if object is not changed since last swap
     *
     * @param Zend_Memory_Container_Movable $container
     * @param integer $id
     *
     */
    private function _swap(Zend_Memory_Container_Movable $container, $id)
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
