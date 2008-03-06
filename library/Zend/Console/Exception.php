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
 * @package    Zend_Console
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Exception
 */
require_once 'Zend/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Console
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Console_Exception extends Zend_Exception
{
    private $_consoleMessage;
    private $_consoleUsage;
    private $_consoleCode;

    public function __construct($consoleMessage = null, $consoleUsage = null, $consoleCode = 1)
    {
        // Overloading would be nice. Oh well.
        $this->_consoleMessage = $consoleMessage;
        $this->_consoleUsage = $consoleUsage;
        $this->_consoleCode = $consoleCode;
    }
    
    public function getConsoleMessage()
    {
        return $this->_consoleMessage;
    }
    
    public function getConsoleUsage()
    {
        return $this->_consoleUsage;
    }
    
    public function prependUsage($str)
    {
        $this->_consoleUsage = $str . $this->_consoleUsage;
        return $this;
    }
    
    public function getConsoleCode()
    {
        return $this->_consoleCode;
    }

}
