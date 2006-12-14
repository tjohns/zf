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
class Zend_TimeSync_Sntp extends Zend_TimeSync_Protocol
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

    /**
     * Writes/receives data to/from the timeserver
     * 
     * @return int unix timestamp
     */
    protected function _query()
    {
        $this->_connect();

        $begin = time();
        fputs($this->_socket, "\n");
        $result = fread($this->_socket, 49);
        $end = time();

        $this->_disconnect();

        if (!$result) {
            throw Zend::exception(
                'Zend_TimeSync_ProtocolException',
                'invalid result returned from server'
            );
        } else {
            $time  = abs(hexdec('7fffffff') - hexdec(bin2hex($result)) - hexdec('7fffffff'));
            $time -= 2208988800;
            // socket delay
            $time -= (($end - $begin) / 2);

            return $time;
        }
    }
}
