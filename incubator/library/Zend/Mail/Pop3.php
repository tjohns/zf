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
 * Zend_Mail_Abstract
 */
require_once 'Zend/Mail/Abstract.php';

/**
 * Zend_Mail_Transport_Pop3
 */
require_once 'Zend/Mail/Transport/Pop3.php';

/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * Zend
 */
require_once 'Zend.php';

/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Pop3 extends Zend_Mail_Abstract 
{
    private $_protocol;


    /**
     *
     * Count messages all messages in current box
     * No flags are supported by POP3 (exceptions is thrown)
     * 
     * @param int filter by flags
     * @return int number of messages
     * @throws Zend_Mail_Exception
     */
    public function countMessages($flags = null) 
    {
        if($flags) {
            throw Zend::exception('Zend_Mail_Exception', 'POP3 does not support flags');
        }
        $this->_protocol->status($count, $null);
        return (int)$count;
    }
    
    /**
     * get a list of messages with number and size
     *
     * @param int number of message
     * @return int|array size of given message of list with all messages as array(num => size)
     */
    public function getSize($id = 0) 
    {
        $id = $id ? $id : null;
        return $this->_protocol->getList($id);
    }
    
    /**
     *
     * get a message with headers and body
     *
     * @param int number of message
     * @return Zend_Mail_Message
     */
    public function getMessage($id) 
    {
        // TODO: error handling!
        $message = $this->_protocol->retrive($id);
        return new Zend_Mail_Message($message);        
    }
    
    /**
     *
     * get a message with only header and $bodyLines lines of body
     *
     * @param int number of message
     * @param int also retrieve this number of body lines
     * @return Zend_Mail_Message 
     */
    public function getHeader($id, $bodyLines = 0) 
    {
        $message = $this->_protocol->top($id, $bodyLines, true);

        if($bodyLines) {
            return new Zend_Mail_Message($message);        
        } else {
            return new Zend_Mail_Message('', $message);        
        }
    }

    /**
     *
     * create instance with parameters
     * Supported paramters are
     *   - host hostname or ip address of POP3 server
     *   - user username
     *   - password password for user 'username' [optional, default = '']
     *   - port port for POP3 server [optional, default = 110]
     *   - ssl 'SSL' or 'TLS' for secure sockets
     *
     * @param  $params array  mail reader specific parameters
     * @throws Zend_Mail_Exception
     */
    public function __construct($params) 
    {
        if($params instanceof Zend_Mail_Transport_Pop3) {
            $this->_protocol = $params;
            return;
        }
    
        if(!isset($params['host']) || !isset($params['user'])) {
            throw Zend::exception('Zend_Mail_Exception', 'need at least a host an user in params');
        }
        $params['password'] = isset($params['password']) ? $params['password'] : '';
        $params['port']     = isset($params['port'])     ? $params['port']     : null;
        $params['ssl']      = isset($params['ssl']) ? $params['ssl'] : false;
        
        $this->_protocol = new Zend_Mail_Transport_Pop3();
        $this->_protocol->connect($params['host'], $params['port'], $params['ssl']);
        $this->_protocol->login($params['user'], $params['password']);
    }
    
    
    /**
     *
     * public destructor
     */    
    public function __destruct() 
    {
        $this->close();
    }
    
    /**
     *
     * Close resource for mail lib. If you need to control, when the resource
     * is closed. Otherwise the destructor would call this.
     *
     */
    public function close() 
    {
        $this->_protocol->logout();
    }
    
    /**
     *
     * Keep the server busy.
     *
     */
    public function noop() 
    {
        return $this->_protocol->noop();
    }
    
    /**
     *
     * Remove a message from server. If you're doing that from a web enviroment
     * you should be careful and use a uniqueid as parameter if possible to 
     * identify the message.
     *
     * @param int number of message
     */
    public function removeMessage($id) 
    {
        $this->_protocol->delete($id);
    }

    /**
     * 
     * Special handling for hasTop. The headers of the first message is
     * retrieved if Top wasn't needed/tried yet.
     * 
     * @see Zend_Mail_Abstract:__get()
     */        
    public function __get($var) 
    {
        if(strtolower($var) == 'hastop') {
            if($this->_protocol->hasTop === null) {
                // need to make a real call, because not all server are honest in their capas
                try {
                    $this->_protocol->top(1, 0, false);
                } catch(Exception $e) {
                    // ignoring error
                }
            }
            return $this->_protocol->hasTop;
        }
        
        return parent::__get($var);
    }
}
