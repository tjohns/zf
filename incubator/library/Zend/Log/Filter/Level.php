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

/** Zend_Log_Filter_Interface */
require_once 'Zend/Log/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_Log_Filter_Level implements Zend_Log_Filter_Interface
{
    /**
     * @var integer
     */
    protected $_level;

    /**
     * Filter out any log messages greater than $level.
     *
     * @param  integer  $level  Maximum log level to pass through the filter
     * @return void
     */
    public function __construct($level)
    {
        $this->_level = $level;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param  string   $message  message for the log
     * @param  integer  $level    log level
     * @return boolean            accepted?
     */
    public function accept($message, $level)
    {
        return $level <= $this->_level;
    }

}
