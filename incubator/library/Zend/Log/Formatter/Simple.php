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
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Log_Formatter_Interface */
require_once 'Zend/Log/Formatter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */ 
class Zend_Log_Formatter_Simple implements Zend_Log_Formatter_Interface
{
    /**
     * @var string
     */
    protected $_format;

    /**
     * Class constructor
     *
     * @param  null|string  $format  Format specifier for log messages
     * @throws InvalidArgumentException
     */
    public function __construct($format = null)
    {
        if ($format === null) {
            $format = '%timestamp% %priority%: %message%' . PHP_EOL;
        }
        
        if (! is_string($format)) {
            throw new InvalidArgumentException('Format must be a string');
        }
        
        $this->_format = $format;
    }

    /**
     * Formats a message to be written by the writer.
     *
     * @param  string   $message   message for the log
     * @param  integer  $priority  priority of message
     * @return string              formatted message
     */
    public function format($message, $priority)
    {
        return str_replace(array('%message%', '%priority%', '%timestamp%'),
                           array($message, $priority, date('c')),
                           $this->_format);
    }

}
