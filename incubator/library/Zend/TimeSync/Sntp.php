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
 * Zend_TimeSync_Exception
 */
require_once 'Zend/TimeSync/Exception.php';

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_TimeSync_Sntp extends Zend_TimeSync_Protocol
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
        if (!$this->_connect()) {
            return false;
        }
                
        fputs($this->_socket, "\n");
        $result = fread($socket, 49);
        fclose($socket);
        
        $this->_disconnect();
        
        if (!$result) {
            return false;
        } else {
            $time  = abs(hexdec('7fffffff') - bin2hex($result) - hexdec('7fffffff'));
            $time -= 2208988800 - $delay;
            
            return $time;
        }
    }
}