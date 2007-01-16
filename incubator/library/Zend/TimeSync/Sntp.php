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
class Zend_TimeSync_Sntp extends Zend_TimeSync_Protocol
{
    private $_delay;
    
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
        return "\n";
    }

    protected function _read()
    {
        $result = fread($this->_socket, 49);
        $this->_delay = ($this->_delay - time()) / 2;
        
    }

    protected function _write($data)
    {
        $this->_connect();
        $this->_delay = time();
        fputs($this->_socket, $data);
    }

    protected function _extract($result)
    {
        $time  = abs(hexdec('7fffffff') - hexdec(bin2hex($result)) - hexdec('7fffffff'));
        $time -= 2208988800;
        // socket delay
        $time -= $this->_delay;

        return $time;
    }
}
