<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Log
 * @subpackage Adapters
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * @package    Zend_Log
 * @subpackage Adapters
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
interface Zend_Log_Adapter_Interface
{
	/**
	 * Open the storage resource.  If the adapter supports buffering, this may not
	 * actually open anything until it is time to flush the buffer.
	 */
	public function open();


	/**
	 * Write a message to the log.  If the adapter supports buffering, the
	 * message may or may not actually go into storage until the buffer is flushed.
	 *
	 * @param $fields     Associative array, contains keys 'message' and 'level' at a minimum.
	 */
	public function write($fields);


	/**
	 * Close the log storage opened by the log adapter.  If the adapter supports
	 * buffering, all log data must be sent to the log before the storage is closed.
	 */
	public function close();


	/**
	 * Sets an option specific to the implementation of the log adapter.
	 *
	 * @param $optionKey       Key name for the option to be changed.  Keys are adapter-specific
	 * @param $optionValue     New value to assign to the option
	 */
    public function setOption($optionKey, $optionValue);
}

