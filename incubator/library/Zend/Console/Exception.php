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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id:$
 */

/**
 * @see Zend_Exception
 */
require_once 'Zend/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Console
 * @uses       Zend_Exception
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Console_Exception extends Zend_Exception
{
    /**
     * $_consoleMessage
     *
     * @var string
     */
    private $_consoleMessage;

    /**
     * $_consoleUsage
     *
     * @var string
     */
    private $_consoleUsage;

    /**
     * $_consoleCode
     *
     * @var integer
     */
    private $_consoleCode;

    /**
     * Constructor
     *
     * @param  string  $consoleMessage
     * @param  string  $consoleUsage
     * @param  integer $consoleCode
     * @return void
     */
    public function __construct($consoleMessage = null, $consoleUsage = null, $consoleCode =1)
    {
        // Overloading would be nice. Oh well.
        $this->_consoleMessage = $consoleMessage;
        $this->_consoleCode = $consoleCode;
    }

    /**
     * getConsoleMessage
     *
     * @return string
     */
    public function getConsoleMessage()
    {
        return $this->_consoleMessage;
    }

    /**
     * getConsoleUsage
     *
     * @return string
     */
    public function getConsoleUsage()
    {
        return $this->_consoleUsage;
    }

    /**
     * prependUsage
     *
     * @param  string $str
     * @return Zend_Console_Exception
     */
    public function prependUsage($str)
    {
        $this->_consoleUsage = $str . $this->_consoleUsage;
        return $this;
    }

    /**
     * getConsoleCode
     *
     * @return integer
     */
    public function getConsoleCode()
    {
        return $this->_consoleCode;
    }
}