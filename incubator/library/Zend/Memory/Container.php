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


/**
 * Memory value container
 *
 * @category   Zend
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Memory_Container implements Zend_Memory_Container_Interface
{
    /**
     * Internal object Id
     *
     * @var integer
     */
    protected $_id;

    /**
     * Memory manager reference
     *
     * @var Zend_Memory_Manager
     */
    private $_memManager;

    /**
     * Object constructor
     *
     * @param Zend_Memory_Manager $memoryManager
     * @param integer $id
     */
    public function __construct($memoryManager, $id)
    {
        $this->_memManager = $memoryManager;
        $this->_id    = $id;
    }

    /**
     * Destroy memory container and remove it from memory manager list
     */
    public function destroy()
    {
        /**
         * We don't clean up swap because of performance considerations
         * Cleaning is performed by Memory Manager destructor
         */

        $this->_memManager->unlink($this->_id);
    }
}
