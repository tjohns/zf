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
 * @package    Zend_Mail
 * @subpackage Client
 * @version    $Id: Client.php 3039 2007-01-27 12:55:48Z shahar $
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
require_once('Zend/Mail/Client/Exception.php');
require_once('Zend/Validate.php');
require_once('Zend/Validate/Hostname.php');

/**
 * Zend_Http_Client is an implemetation of an HTTP client in PHP. The client 
 * supports basic features like sending different HTTP requests and handling
 * redirections, as well as more advanced features like proxy settings, HTTP
 * authentication and cookie persistance (using a Zend_Http_CookieJar object)
 * 
 * @todo Implement proxy settings
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Client
 * @throws     Zend_Mail_Client_Exception
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Mail_Client
{
    const PORT = null;
    const DEBUG = false;
    const EOL = "\r\n";

    const TIMEOUT_CONNECTION = 30;

    protected $_host;
    protected $_port;

    protected $_validHost;

    protected $_socket;
    
    private $_log;

    /**
     * Constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $name  (for use with HELO)
     */
    public function __construct($host = '127.0.0.1', $port = null)
    {
        $this->_validHost = new Zend_Validate();
        $this->_validHost->addValidator(new Zend_Validate_Hostname());

        if (!$this->_validHost->isValid($host)) {
            throw new Zend_Mail_Transport_Exception(join(', ', $validator->getMessage()));
        }
        
        $this->_host = $host;
        $this->_port = $port;
    }
    
    
    /**
     * Class destructor to cleanup open resources
     *
     */
    public function __destruct()
    {
        if (is_resource($this->_socket)) {
            fclose($this->_socket);
        }
    }
    
    /**
     * Connect to the server with the parameters given
     * in the constructor.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    abstract public function connect();

    /**
     * Connect to the server with the parameters given
     * in the constructor.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    protected function _connect($remote)
    {
        $errorNum = 0;
        $errorStr = '';

        // open connection
        $this->_socket = stream_socket_client($remote, $errorNum, $errorStr, self::TIMEOUT_CONNECTION);

        if ($this->_socket === false) {
            if ($errorNum == 0) {
                $errorStr = 'Could not open socket.';
            }
            throw new Zend_Mail_Client_Exception($errorStr);
        }
        
        if (($result = stream_set_timeout($this->_socket, self::TIMEOUT_CONNECTION)) === false) {
            throw new Zend_Mail_Client_Exception('Could not set Stream Timeout.');
        }
    }

    /**
     * Send the given string followed by a LINEEND to the server.
     *
     * @param string $data
     * @throws Zend_Mail_Transport_Exception
     */
    protected function _send($data)
    {
        if (!is_resource($this->_socket)) {
            throw new Zend_Mail_Client_Exception('No connection has been established to ' . $this->_host . '.');
        }
        
        $result = fwrite($this->_socket, $data . self::EOL);
        $this->_log .= $data . self::EOL;

        if ($result === false) {
            throw new Zend_Mail_Client_Exception('Could not write to ' . $this->_host . '.');
        }
        
        return $result;
    }

    /**
     * Get a line from the stream.
     *
     * @return string
     * @throws Zend_Mail_Transport_Exception
     */
    protected function _receive($timeout = null)
    {
        if (!is_resource($this->_socket)) {
            throw new Zend_Mail_Client_Exception('No connection has been established to ' . $this->_host . '.');
        }

        if ($timeout !== null) {
           stream_set_timeout($this->_socket, $timeout);
        }
        
        $this->_log .= $reponse = fgets($this->_socket, 1024);

        $info = stream_get_meta_data($this->_socket);
        
        if (!empty($info['timed_out'])) {
            throw new Zend_Mail_Client_Exception($this->_host . ' has timed out.');
        }
        
        if ($reponse === false) {
            throw new Zend_Mail_Client_Exception('Could not read from ' . $this->_host . '.');
        }

        return $reponse;
    }
    
    /**
     * Read the response from the stream and
     * check for expected return code. throws
     * a Zend_Mail_Transport_Exception if an unexpected code
     * is returned
     *
     * @param int $val1
     * @param int $val2
     * @param int $val3
     * @throws Zend_Mail_Transport_Exception
     */
    protected function _expect($code)
    {
        $this->_response = array();
        
        if (!is_array($code)) {
            $code = array($code);
        }

        do {
            $this->_response[] = $result = $this->_receive();
            sscanf($result, '%d%s', $cmd, $msg);
            
            if ($cmd === null || !in_array($cmd, $code)) {
                throw new Zend_Mail_Client_Exception($result);
            }

        } while ($msg[0] == '-');
    }
}
