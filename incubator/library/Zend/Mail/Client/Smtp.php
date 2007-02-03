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
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Mail_Client
 */
require_once 'Zend/Mail/Client.php';


/**
 * Zend_Mail_Client_Exception
 */
require_once 'Zend/Mail/Client/Exception.php';


/**
 * Smtp implementation of Zend_Mail_Client
 * 
 * Minimum implementation according to RFC2821: EHLO, MAIL FROM, RCPT TO, DATA, RSET, NOOP, QUIT
 * 
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Client
 * @throws     Zend_Mail_Client_Exception
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Client_Smtp extends Zend_Mail_Client
{
    /**
     * Indicates an smtp session has been started by the HELO command
     *
     * @var boolean
     */
    protected $_sess = false;

    
    /**
     * Indicates the HELO command has been issues
     *
     * @var unknown_type
     */
    protected $_helo = false;
    
    
    /**
     * Indicates an smtp AUTH has been issued and authenticated
     *
     * @var unknown_type
     */
    protected $_auth = false;
    
    
    /**
     * Indicates a MAIL command has been issued
     *
     * @var unknown_type
     */
    protected $_mail = false;
    
    
    /**
     * Indicates one or more RCTP commands have been issued
     *
     * @var unknown_type
     */
    protected $_rcpt = false;
    
    
    /**
     * Indicates that DATA has been issued and sent
     *
     * @var unknown_type
     */
    protected $_data = null;

    
    /**
     * Constructor.
     *
     * @param string $host
     * @param int    $port
     */
    public function __construct($host = '127.0.0.1', $port = null)
    {
        // If no port has been specified then check the master PHP ini file. Defaults to 25 if the ini setting is null.
        if ($port == null) {
            if (($port = ini_get('smtp_port')) == '') {
                $port = 25;
            }
        }
        
        parent::__construct($host, $port);
    }

    
    /**
     * Connect to the server with the parameters given in the constructor.
     * 
     * @return boolean
     */
    public function connect()
    {
        return $this->_connect('tcp://' . $this->_host . ':'. $this->_port);
    }


    /**
     * Initiate HELO/EHLO sequence and set flag to indicate valid smtp session
     *
     * @param string $host The client hostname or IP address (default: 127.0.0.1)
     * @throws Zend_Mail_Client_Exception
     */
    public function helo($host = '127.0.0.1')
    {
        // Respect RFC 2821 and disallow HELO attempts if session is already initiated.
        if ($this->_sess === true) {
            throw new Zend_Mail_Client_Exception('Cannot issue HELO to existing session.');
        }

        // Validate client hostname
        if (!$this->_validHost->isValid($host)) {
            throw new Zend_Mail_Client_Exception(join(', ', $this->_validHost->getMessage()));
        }
        
        // Support for older, less-compliant remote servers. Tries multiple attempts of EHLO or HELO.
        try {
            $this->_expect(220, 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
            $this->_send('EHLO ' . $host);
            $this->_expect(250, 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
        } catch (Zend_Mail_Client_Exception $e) {
            $this->_send('HELO ' . $host);
            $this->_expect(250, 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
        } catch (Zend_Mail_Client_Exception $e) {
            throw $e;
        }

        $this->_startSession();
        $this->auth();
    }

    
    /**
     * Issues MAIL command
     *
     * @param  string $from Sender mailbox 
     * @throws Zend_Mail_Client_Exception
     */
    public function mail($from)
    {
        if ($this->_sess !== true) {
            throw new Zend_Mail_Client_Exception('A valid session has not been started.');
        }

        $this->_send('MAIL FROM:<' . $from . '>');
        $this->_expect(250, 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
        
        // Set mail to true, clear recipients and any existing data flags as per 4.1.1.2 of RFC 2821
        $this->_mail = true;
        $this->_rcpt = false;
        $this->_data = false;
    }

    
    /**
     * Issues RCPT command
     *
     * @param string $to Receiver(s) mailbox
     * @throws Zend_Mail_Client_Exception
     */
    public function rcpt($to)
    {
        if ($this->_mail !== true) {
            throw new Zend_Mail_Client_Exception('No sender reverse path has been supplied.');
        }

        // Set rcpt to true, as per 4.1.1.3 of RFC 2821
        $this->_send('RCPT TO:<' . $to . '>');
        $this->_expect(array(250, 251), 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
        $this->_rcpt = true;
    }


    /**
     * Issues DATA command
     *
     * @param string $data
     * @throws Zend_Mail_Client_Exception
     */
    public function data($data)
    {
        // Ensure recipients have been set
        if ($this->_rcpt !== true) {
            throw new Zend_Mail_Client_Exception('No recipient forward path has been supplied.');
        }

        $this->_send('DATA');
        $this->_expect(354, 120); // Timeout set for 2 minutes as per RFC 2821 4.5.3.2
        
        foreach (explode(self::EOL, $data) as $line) {
            if (strpos($line, '.') === 0) {
                // Escape lines prefixed with a '.'
                $line = '.' . $line;
            }
            $this->_send($line);
        }

        $this->_send('.');
        $this->_expect(250, 600); // Timeout set for 10 minutes as per RFC 2821 4.5.3.2
        $this->_data = true;
    }


    /**
     * Issues the RSET command end validates answer
     * 
     * Can be used to restore a clean smtp communication state when a transaction has been cancelled or commencing a new transaction.
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
     * 
     * Not used by Zend_Mail, could be used to keep a connection alive or check if it is still open.
     */
    public function noop()
    {
        $this->_send('NOOP');
        $this->_expect(250, 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
    }


    /**
     * Issues the VRFY command end validates answer
     * 
     * Not used by Zend_Mail.
     *
     * @param string $user User Name or eMail to verify
     */
    public function vrfy($user)
    {
        $this->_send('VRFY ' . $user);
        $this->_expect(array(250, 251, 252), 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
    }


    /**
     * Issues the QUIT command and clears the current session
     */
    public function quit()
    {
        $this->_send('QUIT');
        $this->_expect(221, 300); // Timeout set for 5 minutes as per RFC 2821 4.5.3.2
        $this->_stopSession();
    }


    /**
     * Default authentication method
     * 
     * This default method is implemented by AUTH adapters to properly authenticate to a remote host.
     */
    public function auth()
    {
        if ($this->_auth === true) {
            throw new Zend_Mail_Client_Exception('Already authenticated for this session.');
        }
    }


    /**
     * Closes connection
     */
    public function disconnect()
    {
        $this->_disconnect();
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
