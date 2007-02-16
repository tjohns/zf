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

/** Zend_Log_Exception */
require_once 'Zend/Log/Exception.php';

/** Zend_Log_Filter_Level */
require_once 'Zend/Log/Filter/Level.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_Log
{
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug-level messages

    /**
     * @var array of log levels where the keys are the
     * level priorities and the values are the level names
     */
    private $_levels = array();

    /**
     * @var array of Zend_Log_Writer_Abstract
     */
    private $_writers = array();

    /**
     * @var array of Zend_Log_Filter_Interface
     */
    private $_filters = array();

    /**
     * Class constructor.  Create a new logger
     *
     * @param Zend_Log_Writer_Abstract|null  $writer  default writer
     */
    public function __construct($writer = null)
    {
        $r = new ReflectionClass($this);
        $this->_levels = array_flip($r->getConstants());

        if ($writer !== null) {
            $this->addWriter($writer);
        }
    }

    /**
     * Undefined method handler allows a shortcut:
     *   $log->levelName('message')
     *     instead of
     *   $log->log('message', Zend_Log::LEVELNAME)
     *
     * @param  string  $method  log level name
     * @param  string  $params  message to log
     * @return void
     */
    public function __call($method, $params)
    {
        $level = strtoupper($method);
        if (($level = array_search($level, $this->_levels)) !== false) {
            $this->log(array_shift($params), $level);
        }
    }

    /**
     * Log a message at a level
     *
     * @param  string   $message  Message to log
     * @param  integer  $level    Log level of message
     * @return void
     */
    public function log($message, $level)
    {
        foreach ($this->_filters as $filter) {
            if (!$filter->accept($message, $level)) {
                return;
            }
        }

        foreach ($this->_writers as $writer) {
            $writer->log($message, $level);
        }
    }

    /**
     * Add a custom log level
     *
     * @param  string  $name    Name of level
     * @param  integer  $level  Numeric level
     * @return void
     */
    public function addLevel($name, $level)
    {
        // Log level names must be uppercase for predictability.
        $name = strtoupper($name);

        if (isset($this->_levels[$level])
            || array_search($name, $this->_levels)) {
            throw new Zend_Log_Exception('Existing log levels cannot be overwritten');
        }

        $this->_levels[$level] = $name;
    }

    /**
     * Add a filter that will be applied before all log writers.
     * Before a message will be received by any of the writers, it
     * must be accepted by all filters added with this method.
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
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param  Zend_Log_Writer_Abstract $writer
     * @return void
     */
    public function addWriter($writer)
    {
        $this->_writers[] = $writer;
    }
}
