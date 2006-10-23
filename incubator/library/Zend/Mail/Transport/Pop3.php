<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
 
/**
 * Zend_Mail_Transport_Exception
 */
require_once 'Zend/Mail/Transport/Exception.php';
 
 
/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Transport_Pop3 
{
    /**
     * saves if server supports top
     */
    public $hasTop = null;
    
    /**
     * socket to pop3
     */
    private $_socket;
    
    /**
     * greeting timestamp for apop
     */
    private $_timestamp;


    /**
     * Public constructor
     *
     * @param string $host  hostname of IP address of POP3 server, if given connect() is called
     * @param int    $port  port of POP3 server, default is 110 (995 for ssl)
     * @param bool   $ssl   use ssl?
     */
    public function __construct($host = '', $port = null, $ssl = false) 
    {
        if ($host) {
            $this->connect($host, $port, $ssl);
        }
    }
    
    
    /**
     * Public destructor
     */
    public function __destruct()
    {
        $this->logout();
    }
    
    
    /**
     * Open connection to POP3 server
     *
     * @param  string $host  hostname of IP address of POP3 server
     * @param  int    $port  of POP3 server, default is 110 (995 for ssl)
     * @param  string $ssl   use 'SSL' or 'TLS'
     * @throws Zend_Mail_Transport_Exception
     * @return string welcome message
     */
    public function connect($host, $port = null, $ssl = false) 
    {
        if ($ssl == 'SSL') {
            $host = 'ssl://' . $host;
        }
        
        if ($port === null) {
            $port = $ssl == 'SSL' ? 995 : 110;
        }
    
        $this->_socket = @fsockopen($host, $port);
        if (!$this->_socket) {
            throw new Zend_Mail_Transport_Exception('cannot connect to host');
        }

        $welcome = $this->readResponse();

        strtok($welcome, '<');
        $this->_timestamp = strtok('>');
        if (!strpos($this->_timestamp, '@')) {
            $this->_timestamp = null;
        } else {
            $this->_timestamp = '<' . $this->_timestamp . '>';
        }
        
        if($ssl === 'TLS') {
            $this->request('STLS');
            $result = stream_socket_enable_crypto($this->_socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if(!$result) {
                throw new Zend_Mail_Transport_Exception('cannot enable TLS');
            }
        }
        
        return $welcome;
    }


    /**
     * Send a request
     *
     * @param string $request  your request without newline
     */ 
    public function sendRequest($request) 
    {
        $result = fputs($this->_socket, $request."\n");
        if (!$result) {
            throw new Zend_Mail_Transport_Exception('send failed - connection closed?');
        }
    }


    /**
     *
     * read a response
     *
     * @param  boolean response has multiple lines and should be read until "<nl>.<nl>"
     * @throws Zend_Mail_Transport_Exception
     * @return string response
     */
    public function readResponse($multiline = false) 
    {
        $result = fgets($this->_socket);
        if (!is_string($result)) {
            throw new Zend_Mail_Transport_Exception('read failed - connection closed?');
        }
        
        $result = trim($result);
        if (strpos($result, ' ')) {
            list($status, $message) = explode(' ', $result, 2);
        } else {
            $status = $result;
            $message = '';
        }
        
        if ($status != '+OK') {
            throw new Zend_Mail_Transport_Exception('last request failed');
        }

        if ($multiline) {
            $message = '';
            $line = fgets($this->_socket);
            while ($line && trim($line) != '.') {
                $message .= $line;
                $line = fgets($this->_socket);
            };
        }

        return $message;
    }


    /**
     * Send request and get resposne
     * @see sendRequest(), readResponse()
     *
     * @param  string $request    request
     * @param  bool   $multiline  multiline response?
     * @return string             result from readResponse()
     */
    public function request($request, $multiline = false) 
    {
        $this->sendRequest($request);
        return $this->readResponse($multiline);
    }


    /**
     * End communication with POP3 server (also closes socket)
     */
    public function logout() 
    {
        if(!$this->_socket) {
            return;
        }
        
        try {
            $this->request('QUIT');
        } catch (Zend_Mail_Transport_Exception $e) {
            // ignore error - we're closing the socket anyway
        }
        
        fclose($this->_socket);
        $this->_socket = null;
    }


    /**
     * Get capabilities from POP3 server
     *
     * @return array list of capabilities
     */
    public function capa() 
    {
        $result = $this->request('CAPA', true);
        return explode("\n", $result);
    }


    /**
     * Login to POP3 server. Can use APOP
     *
     * @param  string $user      username
     * @param  string $password  password
     * @param  bool   $try_apop  should APOP be tried?
     * @return void
     */
    public function login($user, $password, $tryApop = true) 
    {
        if ($tryApop && $this->_timestamp) {
            try {
                $this->request("APOP $user " . md5($this->_timestamp . $password));
                return;
            } catch (Zend_Mail_Transport_Exception $e) {
                // ignore 
            }
        }

        $result = $this->request("USER $user");
        $result = $this->request("PASS $password");
    }


    /**
     * Make STAT call for message count and size sum
     *
     * @param  int $messages  out parameter with count of messages
     * @param  int $octets    out parameter with size in octects of messages
     * @return void
     */
    public function status(&$messages, &$octets)
    {
        $messages = 0;
        $octets = 0;
        $result = $this->request('STAT');

        list($messages, $octets) = explode(' ', $result);
    }


    /**
     * Make LIST call for size of message(s)
     *
     * @param  int $msgno  number of message
     * @return int|array   size of given message or list with array(num => size)
     */
    public function getList($msgno = null)
    {
        if ($msgno !== null) {
            $result = $this->request("LIST $msgno");

            list(, $result) = explode(' ', $result);
            return (int)$result;
        }

        $result = $this->request('LIST', true);
        $messages = array();
        $line = strtok($result, "\n");
        while($line) {
            list($no, $size) = explode(' ', trim($line));
            $messages[(int)$no] = (int)$size;
            $line = strtok("\n");
        }

        return $messages;
    }


    /**
     * Make UIDL call for getting a uniqueid
     *
     * @param  int $msgno    number of message
     * @return string|array  uniqueid of message or list with array(num => uniqueid)
     */
    public function uniqueid($msgno = null) 
    {
        if ($msgno !== null) {
            $result = $this->request("UIDL $msgno");

            list(, $result) = explode(' ', $result);
            return $result;
        }

        $result = $this->request('UIDL', true);
        
        $result = explode("\n", $result);
        $messages = array();
        foreach ($result as $line) {
            list($no, $id) = explode(' ', $line);
            $messages[(int)$no] = $id;
        }

        return $messages;
    
    }


    /**
     * Make TOP call for getting headers and maybe some body lines
     * This method also sets hasTop - before it it's not known if top is supported
     *
     * The fallback makes normale RETR call, which retrieves the whole message. Additional
     * lines are not removed.
     * 
     * @param int  $msgno     number of message
     * @param int  $lines     number of wanted body lines (empty line is inserted after header lines)
     * @param bool $fallback  fallback with full retrieve if top is not supported
     * @return string message headers with wanted body lines
     */
    public function top($msgno, $lines = 0, $fallback = false) 
    {
        if ($this->hasTop === false) {
            if ($fallback) {
                return $this->retrive($msgno);
            } else {
                throw new Zend_Mail_Transport_Exception('top not supported and no fallback wanted');
            }
        }
        $this->hasTop = true;

        if (!$lines || (int)$lines < 1) {
            $request = "TOP $msgno";
        } else {
            $request = "TOP $msgno $lines";
        }

        try {
            $result = $this->request($request, true);
        } catch (Zend_Mail_Transport_Exception $e) {
            $this->hasTop = false;
            if ($fallback) {
                $result = $this->retrive($msgno);
            } else {
                throw $e;
            }           
        }

        return $result;
    }


    /**
     * Make a RETR call for retrieving a full message with headers and body
     *
     * @param  int     $msgno  message number
     * @return string          message
     */
    public function retrive($msgno) 
    {
        $result = $this->request("RETR $msgno", true);
        return $result;
    }


    /**
     * Make a NOOP call, maybe needed for keeping the server happy
     */
    public function noop() 
    {
        $this->request('NOOP');
    }


    /**
     * Make a DELE count to remove a message
     */
    public function delete($msgno) 
    {
        $this->request("DELE $msgno");
    }


    /**
     * Make RSET call, which rollbacks delete requests
     */
    public function undelete() 
    {
        $this->request('RSET');
    }
}
