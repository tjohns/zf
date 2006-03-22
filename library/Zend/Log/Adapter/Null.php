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
 * Zend_Log_Adapter_Interface
 */
require_once 'Zend/Log/Adapter/Interface.php';


/**
 * @package    Zend_Log
 * @subpackage Adapters
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Log_Adapter_Null implements Zend_Log_Adapter_Interface
{
    /**
    * Class Constructor
    *
    */
    public function __construct()
    {
        return true;
    }


    /**
    * Class Destructor
    *
    * Always check that the file has been closed and the buffer flushed before destruction.
    */
    public function __destruct()
    {
        $this->flush();
        $this->close();
    }


	/**
	 * Sets an option specific to the implementation of the log adapter.
	 *
	 * @param  $optionKey      Key name for the option to be changed.  Keys are adapter-specific
	 * @param  $optionValue    New value to assign to the option
	 * @return bool            True
	 */
    public function setOption($optionKey, $optionValue)
    {
        return true;
    }


	/**
	 * Sets an option specific to the implementation of the log adapter.
	 *
	 * @param  $optionKey      Key name for the option to be changed.  Keys are adapter-specific
	 * @param  $optionValue    New value to assign to the option
	 * @return bool            True
	 */
	public function open($filename=null, $accessMode='a')
	{
        return true;
	}


	/**
	 * Write a message to the log.  This function really just writes the message to the buffer.
	 * If buffering is enabled, the message won't hit the filesystem until the buffer fills
	 * or is flushed.  If buffering is not enabled, the buffer will be flushed immediately.
	 *
	 * @param  $message    Log message
	 * @param  $level      Log level, one of Zend_Log::LEVEL_* constants
	 * @return bool        True
	 */
    public function write($fields)
    {
	    return true;
	}


	/**
	 * Write a message to the log.  This function really just writes the message to the buffer.
	 *
	 * @param  $message    Log message
	 * @param  $level      Log level, one of Zend_Log::LEVEL_* constants
	 * @return bool        True
	 */
	public function flush()
	{
        return true;
	}


	/**
	 * Closes the file resource for the logfile.  Calling this function does not write any
	 * buffered data into the log, so flush() must be called before close().
	 *
	 * @return bool        True
	 */
	public function close()
	{
	    return true;
	}

}

