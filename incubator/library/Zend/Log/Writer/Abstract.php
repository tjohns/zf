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
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Log_Filter_Priority */
require_once 'Zend/Log/Filter/Priority.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
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
     * Add a filter specific to this writer.
     * 
     * @param  Zend_Log_Filter_Interface  $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new Zend_Log_Filter_Priority($filter);
        }

        $this->_filters[] = $filter;
    }

    /**
     * Log a message to this writer.
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @return void
     */
    public function log($message, $priority)
    {
        foreach ($this->_filters as $filter) {
            if (!$filter->accept($message, $priority)) {
                return;
            }
        }

        $this->write($message, $priority);
    }

    /**
     * Sets an option specific to the implementation of the log writer.
     *
     * @param  $optionKey      Key name for the option to be changed.  Keys are writer-specific
     * @param  $optionValue    New value to assign to the option
     * @return bool            True
     * @throws InvalidArgumentException
     */
    public function setOption($optionKey, $optionValue)
    {
        if (!array_key_exists($optionKey, $this->_options)) {
            throw new InvalidArgumentException("Unknown option \"$optionKey\".");
        }
        $this->_options[$optionKey] = $optionValue;

        return true;
    }

    /**
     * Write a message to the log.
     *
     * @param  $message    Message to log
     * @param  $priority   priority of message
     * @return bool        Always True
     */
    abstract public function write($message, $priority);

}