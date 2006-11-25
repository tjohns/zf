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
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_TimeSync_Protocol
 */
require_once 'Zend/TimeSync/Protocol.php';

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_TimeSync_Ntp extends Zend_TimeSync_Protocol
{    
    protected $_timeserver;
    protected $_port;
    
    public function __construct($timeserver, $port)
    {
        $this->_timeserver = $timeserver;
        $this->_port       = $port;
    }
    
    protected function _query()
    {
        $frac   = microtime();
        $fracb1 = ($frac & 0xff000000) >> 24;
        $fracb2 = ($frac & 0x00ff0000) >> 16;
        $fracb3 = ($frac & 0x0000ff00) >> 8;
        $fracb4 = ($frac & 0x000000ff);
        
        $sec    = time() + 2208988800;
        $secb1  = ($sec & 0xff000000) >> 24;
        $secb2  = ($sec & 0x00ff0000) >> 16;
        $secb3  = ($sec & 0x0000ff00) >> 8;
        $secb4  = ($sec & 0x000000ff);
        
        $ntppacket  = chr(0xd9).chr(0x00).chr(0x0a).chr(0xfa); // Flags
        $ntppacket .= chr(0x00).chr(0x00).chr(0x1c).chr(0x9b); // Root Delay
        $ntppacket .= chr(0x00).chr(0x08).chr(0xd7).chr(0xff); // Clock Dispersion
        $ntppacket .= chr(0x00).chr(0x00).chr(0x00).chr(0x00); // ReferenceClockID
        
        $ntppacket .= chr($secb1) .chr($secb2) .chr($secb3) .chr($secb4);   // Reference Timestamp Seconds
        $ntppacket .= chr($fracb1).chr($fracb2).chr($fracb3).chr($fracb4);  // Reference Timestamp Fractional
        
        $ntppacket .= chr(0x00).chr(0x00).chr(0x00).chr(0x00); // Originate Timestamp Seconds
        $ntppacket .= chr(0x00).chr(0x00).chr(0x00).chr(0x00); // Originate Timestamp Fractional
        
        $ntppacket .= chr(0x00).chr(0x00).chr(0x00).chr(0x00); // Receive Timestamp Seconds
        $ntppacket .= chr(0x00).chr(0x00).chr(0x00).chr(0x00); // Receive Timestamp Fractional
        
        $ntppacket .= chr($secb1) .chr($secb2) .chr($secb3) .chr($secb4);   // Transmit Timestamp Seconds
        $ntppacket .= chr($fracb1).chr($fracb2).chr($fracb3).chr($fracb4);  // Transmit Timestamp Fractional
        
        $this->_connect();
                              
        fwrite($this->_socket, $ntppacket);
        
        $flags     = ord(fread($this->_socket, 1));
        $stratum   = ord(fread($this->_socket, 1));
        $poll      = ord(fread($this->_socket, 1));
        $precision = ord(fread($this->_socket, 1));
        
        $this->_disconnect();
        
        $leap    = ($flags & 0xc0) >> 6; // Leap Indicator bit 1100 0000
        // 0 = no warning, 1 = last min. 61 sec., 2 = last min. 59 sec., 3 = not synconised
        $version = ($flags & 0x38) >> 3; // Version Number bit 0011 1000
        // should be 3
        $mode    = ($flags & 0x07);      // Mode bit 0000 0111
        // 0 = reserved, 1 = symetric active, 2 = symetric passive, 3 = client
        // 4 = server, 5 = broadcast, 6 & 7 = reserved
    }
}
