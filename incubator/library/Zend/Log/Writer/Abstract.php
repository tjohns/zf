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
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
abstract class Zend_Log_Writer_Abstract
{
    /**
     * @var array of key/value pair options
     */
    protected $_options = array();

    /**
     * @var array of Zend_Log_Filter_Interface
     */
    protected $_filters = array();

    /**
     * Class Destructor
     *
     * Flush log buffer on class shutdown.
     */
    public function __destruct()
    {
        $this->flush();
    }

    /**
     * Add a filter specific to this writer.
     * 
     * @param  Zend_Log_Filter_Interface  $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new Zend_Log_Filter_Level($filter);
        }

        $this->_filters[] = $filter;
    }

    /**
     * Log a message to this writer.
     *
     * @param  string   $message  Message to log
     * @param  integer  $level    Log level
     * @return void
     */
    public function log($message, $level)
    {
        foreach ($this->_filters as $filter) {
            if (!$filter->accept($message, $level)) {
                return;
            }
        }

        $this->write($message, $level);
    }

	/**
	 * Sets an option specific to the implementation of the log writer.
	 *
	 * @param  $optionKey      Key name for the option to be changed.  Keys are writer-specific
	 * @param  $optionValue    New value to assign to the option
	 * @return bool            True
	 */
    public function setOption($optionKey, $optionValue)
    {
        if (!array_key_exists($optionKey, $this->_options)) {
            throw new Zend_Log_Exception("Unknown option \"$optionKey\".");
        }
        $this->_options[$optionKey] = $optionValue;

        return true;
    }

    /**
     * Buffer a message to be stored in the storage 
     * implemented by this writer.
     * 
     * @param  string  $message  Message to log
     * @param  string  $level    Log level
     */
    abstract public function write($message, $level);

    /**
     * Flush the buffer to the storage.
     */
    abstract public function flush();
}
