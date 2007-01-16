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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_TimeSync_Ntp extends Zend_TimeSync_Protocol
{    
    /**
     * Class constructor, sets the timeserver and port number
     *
     * @param  string $timeserver
     * @param  int    $port
     * @return void
     */
    public function __construct($timeserver, $port)
    {
        $this->_timeserver = $timeserver;
        $this->_port       = $port;
    }

    protected function _prepare()
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
        
        return $ntppacket;
    }

    protected function _read()
    {
        $flags = ord(fread($this->_socket, 1));
        $info  = stream_get_meta_data($this->_socket);

        if ($info['timed_out']) {
            fclose($this->_socket);
            throw new Zend_TimeSync_Exception("could not connect to '$this->_timeserver' on port '$this->_port', reason: 'server timed out'");
        }

        $result = array(
            'flags'          => $flags,
            'stratum'        => ord(fread($this->_socket, 1)),
            'poll'           => ord(fread($this->_socket, 1)),
            'precision'      => ord(fread($this->_socket, 1)),
            'rootdelay'      => ord(fread($this->_socket, 4)),
            'rootdispersion' => ord(fread($this->_socket, 4)),
            'referenceid'    => ord(fread($this->_socket, 4)),
            'referencestamp' => ord(fread($this->_socket, 4)),
            'referencemicro' => ord(fread($this->_socket, 4)),
            'originatestamp' => ord(fread($this->_socket, 4)),
            'originatemicro' => ord(fread($this->_socket, 4)),
            'receivestamp'   => ord(fread($this->_socket, 4)),
            'receivemicro'   => ord(fread($this->_socket, 4)),
            'transmitstamp'  => ord(fread($this->_socket, 4)),
            'transmitmicro'  => ord(fread($this->_socket, 4)),
            'clientreceived' => 0
        );
        
        $this->_disconnect();
        
        return $result;
    }

    protected function _write($data)
    {
        $this->_connect();

        fwrite($this->_socket, $data);
        stream_set_timeout($this->_socket, Zend_TimeSync::$options['timeout']);
    }

    protected function _extract($binary)
    {
        $leap = ($binary['flags'] & 0xc0) >> 6; // Leap Indicator bit 1100 0000

        // 0 = no warning, 1 = last min. 61 sec., 2 = last min. 59 sec., 3 = not synconised
        switch($leap) {
            case 0 :
                $this->_info['leap'] = '0 - no warning';
                break;
            case 1 :
                $this->_info['leap'] = '1 - last minute has 61 seconds';
                break;
            case 2 :
                $this->_info['leap'] = '2 - last minute has 59 seconds';
                break;
            default:
                $this->_info['leap'] = '3 - not syncronised';
        }

        $this->_info['version'] = ($binary['flags'] & 0x38) >> 3; // Version Number bit 0011 1000
        // should be 3

        $mode = ($binary['flags'] & 0x07); // Mode bit 0000 0111
        // 0 = reserved, 1 = symetric active, 2 = symetric passive, 3 = client
        // 4 = server, 5 = broadcast, 6 & 7 = reserved
        switch($mode) {
            case 1 :
                $this->_info['mode'] = 'symetric active';
                break;
            case 2 :
                $this->_info['mode'] = 'symetric passive';
                break;
            case 3 :
                $this->_info['mode'] = 'client';
                break;
            case 4 :
                $this->_info['mode'] = 'server';
                break;
            case 5 :
                $this->_info['mode'] = 'broadcast';
                break;
            default:
                $this->_info['mode'] = 'reserved';
                break;
        }

        $ntpserviceid = 'Unknown Stratum ' . $binary['stratum'] . ' Service';
        $refid = strtoupper($binary['referenceid']);

        switch($binary['stratum']) {
            case 0:
                if (substr($refid, 0, 3) == 'DCN') {
                    $ntpserviceid = 'DCN routing protocol';
                } else if (substr($refid, 0, 4) == 'NIST') {
                    $ntpserviceid = 'NIST public modem';
                } else if (substr($refid, 0, 3) == 'TSP') {
                    $ntpserviceid = 'TSP time protocol';
                } else if (substr($refid, 0, 3) == 'DTS') {
                    $ntpserviceid = 'Digital Time Service';
                }
                break;
            case 1:
                if (substr($refid, 0, 4) == 'ATOM') {
                    $ntpserviceid = 'Atomic Clock (calibrated)';
                } else if (substr($refid, 0, 3) == 'VLF') {
                    $ntpserviceid = 'VLF radio';
                } else if ($refid == 'CALLSIGN') {
                    $ntpserviceid = 'Generic radio';
                } else if (substr($refid, 0, 4) == 'LORC') {
                    $ntpserviceid = 'LORAN-C radionavigation';
                } else if (substr($refid, 0, 4) == 'GOES') {
                    $ntpserviceid = 'GOES UHF environment satellite';
                } else if (substr($refid, 0, 3) == 'GPS') {
                    $ntpserviceid = 'GPS UHF satellite positioning';
                }
                break;
            default:
                $ntpserviceid  = ord(substr($binary['referenceid'], 0, 1));
                $ntpserviceid .= ".";
                $ntpserviceid .= ord(substr($binary['referenceid'], 1, 1));
                $ntpserviceid .= ".";
                $ntpserviceid .= ord(substr($binary['referenceid'], 2, 1));
                $ntpserviceid .= ".";
                $ntpserviceid .= ord(substr($binary['referenceid'], 3, 1));
                break;
        }

        $this->_info['ntpid'] = $ntpserviceid;

        switch($binary['stratum']) {
            case 0:
                $this->_info['stratum'] = 'undefined';
                break;
            case 1:
                $this->_info['stratum'] = 'primary reference';
                break;
            default:
                $this->_info['stratum'] = 'secondary reference';
                break;
        }

        $this->_info['rootdelay']          = $binary['rootdelay'] >> 15;
        $this->_info['rootdelayfrac']      = ($binary['rootdelay'] << 17) >> 17;
        $this->_info['rootdispersion']     = $binary['rootdispersion'] >> 15;
        $this->_info['rootdispersionfrac'] = ($binary['rootdispersion'] << 17) >> 17;

        // seconds for message to the server
        $original  = (float) $binary['originatestamp'];
        $original += (float) $binary['originatemicro'] / 4294967296;
        $received  = (float) $binary['receivestamp'];
        $received += (float) $binary['receivemicro'] / 4294967296;
        $transmit  = (float) $binary['transmitstamp'];
        $transmit += (float) $binary['transmitmicro'] / 4294967296;
        $roundtrip = ($binary['clientreceived'] - $original) - ($transmit - $received);
        
        $this->_info['roundtrip'] = $roundtrip / 2;

        // seconds to add for local clock
        $offset = $received - $original + $transmit - $binary['clientreceived'];
        $this->_info['offset'] = $offset / 2;

        $time = time() - $offset;

        return $time;
    }
}
