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
 
 require_once('Zend/Mail/Client.php');

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
class Zend_Mail_Client_Smtp extends Zend_Mail_Client
{
    protected $_sess            = false;
    protected $_helo            = false;
    protected $_auth            = false;
    protected $_mail            = false;
    protected $_rcpt            = false;
    protected $_data            = null;

    /**
     * Constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $name  (for use with HELO)
     */
    public function __construct($host = '127.0.0.1', $port = null)
    {
        if ($port == null) {
            if (($port = ini_get('smtp_port')) == '') {
                $port = 25;
            }
        }
        
        parent::__construct($host, $port);
    }

    /**
     * Connect to the server with the parameters given
     * in the constructor.
     */
    public function connect()
    {
        $this->_connect('tcp://' . $this->_host . ':'. $this->_port);
    }


    /**
     * Initiate HELO/EHLO sequence and set flag to indicate valid SMTP session
     *
     * @throws Zend_Mail_Client_Exception
     */
    public function helo($host = '127.0.0.1')
    {
        if ($this->_sess === true) {
            throw new Zend_Mail_Client_Exception('Cannot issue HELO to existing session.');
        }

        if (!$this->_validHost->isValid($host)) {
            throw new Zend_Mail_Client_Exception(join(', ', $validator->getMessage()));
        }
        
        try {
            $this->_expect(array(220));
            $this->_send('EHLO ' . $host);
            $this->_expect(array(250));
        } catch (Zend_Mail_Client_Exception $e) {
            $this->_send('HELO ' . $host);
            $this->_expect(array(250));
        }

        $this->_startSession();
        $this->auth();
    }

    /**
     * Issues MAIL command
     *
     * @throws Zend_Mail_Client_Exception
     */
    public function mail($from)
    {
        if ($this->_sess !== true) {
            throw new Zend_Mail_Client_Exception('A valid session has not been started.');
        }

        $this->_send('MAIL FROM:<' . $from . '>');
        $this->_expect(250);
        $this->_mail = true;
        $this->_rcpt = false;
        $this->_data = false;
    }

    /**
     * Issues RCPT command
     *
     * @throws Zend_Mail_Client_Exception
     */
    public function rcpt($to)
    {
        if ($this->_mail !== true) {
            throw new Zend_Mail_Client_Exception('No sender reverse path has been supplied.');
        }

        $this->_send('RCPT TO:<' . $to . '>');
        $this->_expect(array(250, 251));
        $this->_rcpt = true;
        $this->_data = false;
    }


    /**
     * Issues DATA command
     *
     * @throws Zend_Mail_Client_Exception
     */
    public function data($data)
    {
        if ($this->_rcpt !== true) {
            throw new Zend_Mail_Client_Exception('No recipient forward path has been supplied.');
        }

        $this->_send('DATA');
        $this->_expect(354);
        foreach (explode(self::EOL, $data) as $line) {
            if (strpos($line, '.') === 0) {
                // Escape lines prefixed with a '.'
                $line = '.' . $line;
            }
            $this->_send($line);
        }
        $this->_send('.');
        $this->_expect(250);
        $this->_data = true;
    }


    /**
     * Issues the RSET command end validates answer
     * Not used by Zend_Mail, can be used to restore a clean
     * smtp communication state when a transaction has
     * been cancelled.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function rset()
    {
        $this->_send('RSET');
        $this->_expect(250);
        $this->_mail = false;
        $this->_rcpt = false;
        $this->_data = false;
    }


    /**
     * Issues the NOOP command end validates answer
     * Not used by Zend_Mail, could be used to keep a connection
     * alive or check if it is still open.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function noop()
    {
        $this->_send('NOOP');
        $this->_expect(250);
    }


    /**
     * Issues the VRFY command end validates answer
     * The calling method needs to evaluate $this->lastResponse
     * This function was implemented for completeness only.
     * It is not used by Zend_Mail.
     *
     * @param string $user User Name or eMail to verify
     * @throws Zend_Mail_Transport_Exception
     */
    public function vrfy($user)
    {
        $this->_send('VRFY ' . $user);
        $this->_expect(array(250, 251, 252));
    }


    /**
     * Issues the QUIT command and validates answer
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function quit()
    {
        $this->_send('QUIT');
        $this->_expect(221);
        $this->_stopSession();
    }


    /**
     * Default authentication ( = none)
     */
    public function auth()
    {
        if ($this->_auth === true) {
            throw new Zend_Mail_Client_Exception('Already authenticated for this session.');
        }
    }


    /**
     * Issues QUIT and closes stream.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function disconnect()
    {
        $this->quit();
    }

    /**
     * Start mail session
     */
    protected function _startSession()
    {
        $this->_sess = true;
    }

    /**
     * Stop mail session
     */
    protected function _stopSession()
    {
        $this->_sess = false;
    }
}
