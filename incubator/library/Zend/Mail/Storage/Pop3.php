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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Mail_Storage_Abstract
 */
require_once 'Zend/Mail/Storage/Abstract.php';

/**
 * Zend_Mail_Protocol_Pop3
 */
require_once 'Zend/Mail/Protocol/Pop3.php';

/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * Zend_Mail_Storage_Exception
 */
require_once 'Zend/Mail/Storage/Exception.php';

/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Storage_Pop3 extends Zend_Mail_Storage_Abstract
{
    private $_protocol;


    /**
     *
     * Count messages all messages in current box
     * No flags are supported by POP3 (exceptions is thrown)
     *
     * @param int filter by flags
     * @return int number of messages
     * @throws Zend_Mail_Storage_Exception
     * @throws Zend_Mail_Protocol_Exception
     */
    public function countMessages($flags = null)
    {
        if ($flags) {
            throw new Zend_Mail_Storage_Exception('POP3 does not support flags');
        }
        $this->_protocol->status($count, $null);
        return (int)$count;
    }

    /**
     * get a list of messages with number and size
     *
     * @param int number of message
     * @return int|array size of given message of list with all messages as array(num => size)
     * @throws Zend_Mail_Protocol_Exception
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
     * @throws Zend_Mail_Protocol_Exception
     */
    public function getMessage($id)
    {
        $bodyLines = 0;
        $message = $this->_protocol->top($id, $bodyLines, true);

        return new Zend_Mail_Message(array('handler' => $this, 'id' => $id, 'headers' => $message,
                                           'noToplines' => $bodyLines < 1));
    }

    /*
     * @throws Zend_Mail_Storage_Exception
     * @throws Zend_Mail_Protocol_Exception
     */
    public function getRaw($id, $part)
    {
        // TODO: indexes for header and content should be changed to negative numbers
        switch ($part) {
            case 'header':
                return $this->_protocol->top($id, 0, true);
                break;
            case 'content':
                $content = $this->_protocol->retrive($id);
                // TODO: find a way to avoid decoding the headers
                Zend_Mime_Decode::splitMessage($content, $null, $body);
                return $body;
                break;
            default:
                // fall through
        }

        // TODO: check for number or mime type
        throw new Zend_Mail_Storage_Exception('part not found');
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
     * @throws Zend_Mail_Storage_Exception
     * @throws Zend_Mail_Protocol_Exception
     */
    public function __construct($params)
    {
        $this->_has['fetchPart'] = false;

        if ($params instanceof Zend_Mail_Protocol_Pop3) {
            $this->_protocol = $params;
            return;
        }

        if (!isset($params['user'])) {
            throw new Zend_Mail_Storage_Exception('need at least user in params');
        }

        $params['host']     = isset($params['host'])     ? $params['host']     : 'localhost';
        $params['password'] = isset($params['password']) ? $params['password'] : '';
        $params['port']     = isset($params['port'])     ? $params['port']     : null;
        $params['ssl']      = isset($params['ssl']) ? $params['ssl'] : false;

        $this->_protocol = new Zend_Mail_Protocol_Pop3();
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
     * @return null
     */
    public function close()
    {
        $this->_protocol->logout();
    }

    /**
     *
     * Keep the server busy.
     *
     * @return null
     * @throws Zend_Mail_Protocol_Exception
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
     * @return null
     * @throws Zend_Mail_Protocol_Exception
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
     * @see Zend_Mail_Storage_Abstract:__get()
     * @throws Zend_Mail_Storage_Exception
     */
    public function __get($var)
    {
        if (strtolower($var) == 'hastop') {
            if ($this->_protocol->hasTop === null) {
                // need to make a real call, because not all server are honest in their capas
                try {
                    $this->_protocol->top(1, 0, false);
                } catch(Zend_Mail_Exception $e) {
                    // ignoring error
                }
            }
            return $this->_protocol->hasTop;
        }

        return parent::__get($var);
    }
}
