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
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Mail_Client_Smtp
 */
require_once 'Zend/Mail/Client/Smtp.php';


/**
 * Zend_Mail_Transport_Abstract
 */
require_once 'Zend/Mail/Transport/Abstract.php';


/**
 * SMTP connection object
 * minimum implementation according to RFC2821:
 * EHLO, MAIL FROM, RCPT TO, DATA, RSET, NOOP, QUIT
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_Smtp extends Zend_Mail_Transport_Abstract {

    protected $_host;
    protected $_port = null;
    protected $_name = 'localhost';
    protected $_auth;
    protected $_username;
    protected $_password;

    /**
     * Instance of Zend_Mail_Client_Smtp
     *
     * @var Stream
     */
    protected $_client;

    /**
     * Constructor.
     *
     * @param string $host
     * @param int $port
     * @param mixed $config
     */
    public function __construct($host = '127.0.0.1', $config = null)
    {
        $this->_host = $host;

        if (is_array($config)) {
            if (isset($config['name'])) {
                $this->_name = $config['name'];
            }
            if (isset($config['port'])) {
                $this->_port = $config['port'];
            }
            if (isset($config['auth'])) {
                $this->_auth = $config['auth'];
            }
            if (isset($config['username'])) {
                $this->_username = $config['username'];
            }
            if (isset($config['password'])) {
                $this->_password = $config['password'];
            }
        }
    }


    /**
     * Class destructor to ensure all open connections are closed
     *
     */
    public function __destruct()
    {
        if ($this->_client instanceof Zend_Mail_Client_Smtp) {
            $this->_client->quit();
        }
    }
    

    /**
     * Send an email through the SMTP client adapter
     */
    public function _sendMail()
    {
        if ($this->_client === null) {
            if ($this->_auth) {
                $class = 'Zend_Mail_Client_Smtp_Auth_' . ucwords($this->_auth);
                Zend::loadClass($class);
                $this->_client = new $class($this->_host, $this->_port,
                                            $this->_username, $this->_password);
            } else {
                $this->_client = new Zend_Mail_Client_Smtp($this->_host, $this->_port);
            }
            $this->_client = new Zend_Mail_Client_Smtp($this->_host, $this->_port);
            $this->_client->connect();
            $this->_client->helo($this->_name);
        } else {
            $this->_client->rset();
        }

        $this->_client->mail($this->_mail->getReturnPath());

        foreach ($this->_mail->getRecipients() as $recipient) {
            $this->_client->rcpt($recipient);
        }

        $this->_client->data($this->header . $this->EOL . $this->body);
    }
}
