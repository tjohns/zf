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
 * Zend
 */
require_once 'Zend.php';

/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Transport_Imap
{
    /**
     * socket to imap server
     */
    private $_socket;


    /**
     * Public constructor
     *
     * @param string $host  hostname of IP address of IMAP server, if given connect() is called
     * @param int    $port  port of IMAP server, default is 143 (993 for ssl)
     * @param bool   $ssl   use ssl?
     */
    function __construct($host = '', $port = null, $ssl = false)
    {
        if($host) {
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
     * @param  int    $port  of IMAP server, default is 143 (993 for ssl)
     * @param  string $ssl   use 'SSL' or 'TLS'
     * @throws Zend_Mail_Transport_Exception
     * @return string welcome message
     */
    public function connect($host, $port = null, $ssl = false)
    {
        if ($ssl == 'SSL') {
            $host = 'ssl://' . $host;
        }

        if($port === null) {
            $port = $ssl === 'SSL' ? 993 : 143;
        }

        $this->_socket = @fsockopen($host, $port);
        if(!$this->_socket) {
            throw Zend::exception('Zend_Mail_Transport_Exception', 'cannot connect to host');
        }

        if(!$this->_assumedNextLine('* OK')) {
            throw Zend::exception('Zend_Mail_Transport_Exception', 'host doesn\'t allow connection');
        }

        if($ssl === 'TLS') {
            $result = $this->requestAndResponse('STARTTLS');
            $result = $result && stream_socket_enable_crypto($this->_socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if(!$result) {
                throw Zend::exception('Zend_Mail_Transport_Exception', 'cannot enable TLS');
            }
        }
    }

    /**
     * get the next line from socket with error checking, but nothing else
     *
     * @return string next line
     * @throws Zend_Mail_Transport_Exception
     */
    private function _nextLine()
    {
        $line = fgets($this->_socket);
        if($line === false) {
            throw Zend::exception('Zend_Mail_Transport_Exception', 'cannot read - connection closed?');
        }

        return $line;
    }

    /**
     * get next line and assume it starts with $start. some requests give a simple
     * feedback so we can quickly check if we can go on.
     *
     * @param string $start the first bytes we assume to be in the next line
     * @return bool line starts with $start
     * @throws Zend_Mail_Transport_Exception
     */
    private function _assumedNextLine($start)
    {
        $line = $this->_nextLine();
        return strpos($line, $start) === 0;
    }

    /**
     * get next line and split the tag. that's the normal case for a response line
     *
     * @param string $tag tag of line is returned by reference
     * @return string next line
     * @throws Zend_Mail_Transport_Exception
     */
    private function _nextTaggedLine(&$tag)
    {
        $line = $this->_nextLine();

        // seperate tage from line
        list($tag, $line) = explode(' ', $line, 2);

        return $line;
    }

    /**
     * split a given line in tokens. a token is literal of any form or a list
     *
     * @param string $line line to decode
     * @return array tokens, literals are returned as string, lists as array
     */
    private function _decodeLine($line)
    {
        $tokens = array();
        $stack = array();

        /*
            We start to decode the response here. The unterstood tokens are:
                literal
                "literal" or also "lit\\er\"al"
                {bytes}<NL>literal
                (literals*)
            All tokens are returned in an array. Literals in braces (the last unterstood
            token in the list) are returned as an array of tokens. I.e. the following response:
                "foo" baz {3}<NL>bar ("f\\\"oo" bar)
            would be returned as:
                array('foo', 'baz', 'bar', array('f\\\"oo', 'bar'));
        */
        //  replace any trailling <NL> including spaces with a single space
        $line = rtrim($line) . ' ';
        while(($pos = strpos($line, ' ')) !== false) {
            $token = substr($line, 0, $pos);
            while($token[0] == '(') {
                array_push($stack, $tokens);
                $tokens = array();
                $token = substr($token, 1);
            }
            if($token[0] == '"') {
                if(preg_match('%^"((.|\\\\|\\")*)"%', $line, $matches)) {
                    $tokens[] = $matches[1];
                    $line = substr($line, $pos + strlen($matches[1]) + 1);
                    continue;
                }
            }
            if($token[0] == '{') {
                $endPos = strpos($token, '}');
                $chars = substr($token, 1, $endPos - 1);
                if(is_numeric($chars)) {
                    $token = '';
                    while(strlen($token) < $chars) {
                        $token .= $this->_nextLine();
                    }
                    $line = '';
                    if(strlen($token) > $chars) {
                        $line = substr($token, $chars);
                        $token = substr($token, 0, $chars);
                    } else {
                        $line .= $this->_nextLine();
                    }
                    $tokens[] = $token;
                    $line = trim($line) . ' ';
                    continue;
                }
            }
            if($stack && $token[strlen($token) - 1] == ')') {
                // closing braces are not seperated by spaces, so we need to count them
                $braces = strlen($token);
                $token = rtrim($token, ')');
                // only count braces if more than one
                $braces -= strlen($token) + 1;
                // only add if token had more than just closing braces
                if($token) {
                    $tokens[] = $token;
                }
                $token = $tokens;
                $tokens = array_pop($stack);
                // special handline if more than one closing brace
                while($braces-- > 0) {
                    $tokens[] = $token;
                    $token = $tokens;
                    $tokens = array_pop($stack);
                }
            }
            $tokens[] = $token;
            $line = substr($line, $pos + 1);
        }

        // maybe the server forgot to send some closing braces
        while($stack) {
            $child = $tokens;
            $tokens = array_pop($stack);
            $tokens[] = $child;
        }

        return $tokens;
    }

    /**
     * read a response "line" (could also be more than one real line if response has {..}<NL>)
     * and do a simple decode
     *
     * @param array|string  $tokens    decoded tokens are returned by reference, if $dontParse
     *                                  is true the unparsed line is returned here
     * @param string        $wantedTag check for this tag for response code. Default '*' is
     *                                  continuation tag.
     * @param bool          $dontParse if true only the unparsed line is returned $tokens
     * @return bool if returned tag matches wanted tag
     */
    public function readLine(&$tokens = array(), $wantedTag = '*', $dontParse = false)
    {
        $line = $this->_nextTaggedLine($tag);
        if(!$dontParse) {
            $tokens = $this->_decodeLine($line);
        } else {
            $tokens = $line;
        }

        // if tag is wanted tag we might be at the end of a multiline response
        return $tag == $wantedTag;
    }

    /**
     * read all lines of response until given tag is found (last line of response)
     *
     * @param string       $tag       the tag of your request
     * @param string|array $filter    you can filter the response so you get only the
     *                                 given response lines
     * @param bool         $dontParse if true every line is returned unparsed instead of
     *                                 the decoded tokens
     * @return null|bool|array tokens if success, false if error, null if bad request
     */
    public function readResponse($tag, $dontParse = false)
    {
        $lines = array();
        while(!$this->readLine($tokens, $tag, $dontParse)) {
            $lines[] = $tokens;
        }
        if($dontParse) {
            // last to chars are still needed for response code
            $tokens = array(substr($tokens, 0, 2));
        }

        // last line has response code
        if($tokens[0] == 'OK') {
            return $lines ? $lines : true;
        } else if($tokens[0] == 'NO'){
            return false;
        }
        return null;
    }

    /**
     * send a request
     *
     * @param string $command your request command
     * @param array  $tokens  additional parameters to command, use escapeString() to prepare
     * @param string $tag     provide a tag otherwise an autogenerated is returned
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function sendRequest($command, $tokens = array(), &$tag = null)
    {
        if(!$tag) {
            $tag = 'TAG' . rand(100, 999);
        }

        fputs($this->_socket, $tag . ' ' . $command);

        foreach($tokens as $token) {
            if(is_array($token)) {
                fputs($this->_socket, ' ' . $token[0] . "\r\n");
                if(!$this->_assumedNextLine('+ OK')) {
                    throw Zend::exception('Zend_Mail_Transport_Exception', 'cannot send literal string');
                }
                fputs($this->_socket, $token[1]);
            } else {
                fputs($this->_socket, ' ' . $token);
            }
        }

        fputs($this->_socket, "\r\n");
    }

    /**
     * send a request and get response at once
     *
     * @param string $command   command as in sendRequest()
     * @param array  $tokens    parameters as in sendRequest()
     * @param bool   $dontParse if true unparsed lines are returned instead of tokens
     * @return mixed response as in readResponse()
     */
    public function requestAndResponse($command, $tokens = array(), $dontParse = false)
    {
        $this->sendRequest($command, $tokens, $tag);
        $response = $this->readResponse($tag, $dontParse);

        return $response;
    }

    /**
     * escape one or more literals i.e. for sendRequest
     *
     * @param string|array $string the literal/-s
     * @return string|array escape literals, literals with newline ar returned
     *                      as array('{size}', 'string');
     */
    public function escapeString($string)
    {
        if(func_num_args() < 2) {
            if(strpos($string, "\n") !== false) {
                return array('{' . strlen($string) . '}', $string);
            } else {
                return '"' . str_replace(array('\\', '"'), array('\\\\', '\\"'), $string) . '"';
            }
        }
        $result = array();
        foreach(func_get_args() as $string) {
            $result[] = $this->escapeString($string);
        }
        return $result;
    }

    /**
     * escape a list with literals or lists
     *
     * @param array $list list with literals or lists as PHP array
     * @return string escaped list for imap
     */
    public function escapeList($list)
    {
        $result = array();
        foreach($list as $k => $v) {
            if(!is_array($v)) {
//              $result[] = $this->escapeString($v);
                $result[] = $v;
                continue;
            }
            $result[] = $this->escapeList($v);
        }
        return '(' . implode(' ', $result) . ')';
    }

    /**
     * Login to IMAP server.
     *
     * @param  string $user      username
     * @param  string $password  password
     * @return bool success
     */
    public function login($user, $password)
    {
        return $this->requestAndResponse('LOGIN', $this->escapeString($user, $password), true);
    }

    /**
     * logout of imap server
     *
     * @return bool success
     */
    public function logout()
    {
        $result = false;
        if($this->_socket) {
            try {
                $result = $this->requestAndResponse('LOGOUT', array(), true);
            } catch (Zend_Mail_Transport_Exception $e) {
                // ignoring exception
            }
            fclose($this->_socket);
            $this->_socket = null;
        }
        return $result;
    }


    /**
     * Get capabilities from IMAP server
     *
     * @return array list of capabilities
     */
    public function capability()
    {
        $response = $this->requestAndResponse('CAPABILITY');

        if(!$response) {
            return $response;
        }

        $capabilities = array();
        foreach($response as $line) {
            $capabilities = array_merge($capabilities, $line);
        }
        return $capabilities;
    }

    /**
     * Examine and select have the same response. The common code for both
     * is in this method
     *
     * @param string can be 'EXAMINE' or 'SELECT' and this is used as command
     * @param string which folder to change to or examine
     *
     * @return bool|array false if error, array with returned information
     *                    otherwise (flags, exists, recent, uidvalidity)
     */
    public function examineOrSelect($command = 'EXAMINE', $box = 'INBOX')
    {
        $this->sendRequest($command, (array)$this->escapeString($box), $tag);

        $result = array();
        while(!$this->readLine($tokens, $tag)) {
            if($tokens[0] == 'FLAGS') {
                array_shift($tokens);
                $result['flags'] = $tokens;
                continue;
            }
            switch($tokens[1]) {
                case 'EXISTS':
                case 'RECENT':
                    $result[strtolower($tokens[1])] = $tokens[0];
                    break;
                case '[UIDVALIDITY':
                    $result['uidvalidity'] = (int)$tokens[2];
                    break;
                default:
                    // ignore
            }
        }

        if($tokens[0] != 'OK') {
            return false;
        }
        return $result;
    }

    /**
     * change folder
     *
     * @param string     $box change to this folder
     * @return bool|array see examineOrselect()
     */
    public function select($box = 'INBOX')
    {
        return $this->examineOrSelect('SELECT', $box);
    }

    /**
     * examine folder
     *
     * @param string     $box examine this folder
     * @return bool|array see examineOrselect()
     */
    public function examine($box = 'INBOX')
    {
        return $this->examineOrSelect('EXAMINE', $box);
    }

    /**
     * fetch one or more items of one or more messages
     *
     * @param string|array $items items to fetch from message(s) as string (if only one item)
     *                             or array of strings
     * @param int          $from  message for items or start message if $to !== null
     * @param int|null     $to    if null only one message ($from) is fetched, else it's the
     *                             last message, INF means last message avaible
     * @return string|array if only one item of one message is fetched it's returned as string
     *                      if items of one message are fetched it's returned as (name => value)
     *                      if one items of messages are fetched it's returned as (msgno => value)
     *                      if items of messages are fetchted it's returned as (msgno => (name => value))
     */
    public function fetch($items, $from, $to = null)
    {
        if($to === null) {
            $set = (int)$from;
        } else if(is_array($from)) {
            $set = implode(',', $from);
        } else if($to === INF) {
            $set = (int)$from . ':*';
        } else {
            $set = (int)$from . ':' . (int)$to;
        }

        $items = (array)$items;
        $itemList = $this->escapeList($items);

        $this->sendRequest('FETCH', array($set, $itemList), $tag);

        $result = array();
        while(!$this->readLine($tokens, $tag)) {
            if($tokens[1] != 'FETCH') {
                continue;
            }
            if($to === null && $tokens[0] != $from) {
                continue;
            }
            if(count($items) == 1) {
                $data = next($tokens[2]);
            } else {
                $data = array();
                while(key($tokens[2]) !== null) {
                    $data[current($tokens[2])] = next($tokens[2]);
                    next($tokens[2]);
                }
            }
            if($to === null && $tokens[0] == $from) {
                return $data;
            }
            $result[$tokens[0]] = $data;
        }

        if($to === null) {
            throw Zend::exception('Zend_Mail_Transport_Exception', 'the single id was not found in response');
        }

        return $result;
    }
}