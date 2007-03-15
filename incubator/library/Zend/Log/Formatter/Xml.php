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
class Zend_Log_Formatter_Xml implements Zend_Log_Formatter_Interface
{
    /**
     * @var array of options
     */
    protected $_options = array('elementEntry'     => 'log',
                                'elementTimestamp' => 'timestamp',
                                'elementMessage'   => 'message',
                                'elementPriority'  => 'priority',
                                'lineEnding'       => PHP_EOL);

    /**
     * Class constructor
     *
     * @param array $options  Options for specifying XML format
     */
    public function __construct($options = array())
    {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * Formats a message to be written by the writer.
     *
     * @param  string   $message  message for the log
     * @param  integer  $priority priority of message
     * @return string             formatted message
     */
    public function format($message, $priority)
    {
        $dom = new DOMDocument();

        $elt = $dom->appendChild(new DOMElement($this->_options['elementEntry']));
        $elt->appendChild(new DOMElement($this->_options['elementTimestamp'], date('c')));
        $elt->appendChild(new DOMElement($this->_options['elementMessage'], $message));
        $elt->appendChild(new DOMElement($this->_options['elementPriority'], $priority));        
        
        $xml = $dom->saveXML();
        $xml = preg_replace('/<\?xml version="1.0"( encoding="[^\"]*")?\?>\n/u', '', $xml);
        
        return $xml . $this->_options['lineEnding'];
    }

}
