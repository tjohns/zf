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
 * @version    $Id$
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
 * 
 * Loads an instance of Zend_Mail_Client_Smtp and forwards smtp transactions
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_Smtp extends Zend_Mail_Transport_Abstract
{
    /**
     * Remote smtp hostname or i.p.
     *
     * @var string
     */
    protected $_host;
    
    
    /**
     * Port number
     *
     * @var integer|null
     */
    protected $_port;
    
    
    /**
     * Local client hostname or i.p.
     *
     * @var string
     */
    protected $_name = 'localhost';
    
    
    /**
     * Authentication type OPTIONAL
     *
     * @var string
     */
    protected $_auth;
    
    
    /**
     * Config options for authentication
     *
     * @var array
     */
    protected $_config;

    
    /**
     * Instance of Zend_Mail_Client_Smtp
     *
     * @var Zend_Mail_Client_Smtp
     */
    protected $_client;

    
    /**
     * Constructor.
     *
     * @param string $host OPTIONAL (Default: 127.0.0.1)
     * @param array|null $config OPTIONAL (Default: null)
     */
    public function __construct($host = '127.0.0.1', Array $config = array())
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }
        if (isset($config['port'])) {
            $this->_port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->_auth = $config['auth'];
        }

        $this->_host = $host;
        $this->_config = $config;
    }


    /**
     * Class destructor to ensure all open connections are closed
     */
    public function __destruct()
    {
        if ($this->_client instanceof Zend_Mail_Client_Smtp) {
            $this->_client->quit();
            $this->_client->disconnect();
        }
    }
    
    
    /**
     * Sets the client object
     * 
     * @param Zend_Mail_Client $client
     */
    public function setClient(Zend_Mail_Client $client)
    {
        $this->_client = $client;
    }
    
    
    /**
     * Gets the client object
     * 
     * @return Zend_Mail_Client|null
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Send an email via the SMTP client adapter
     */
    public function _sendMail()
    {
        // If sending multiple messages per session use existing adapter
        if (!($this->_client instanceof Zend_Mail_Client_Smtp)) {
            
            // Check if authentication is required
            if ($this->_auth) {
                $class = 'Zend_Mail_Client_Smtp_Auth_' . ucwords($this->_auth);
                Zend::loadClass($class);
                $this->setClient(new $class($this->_host, $this->_port, $this->_config));
            } else {
                $this->setClient(new Zend_Mail_Client_Smtp($this->_host, $this->_port));
            }           
            $this->_client->connect();
            $this->_client->helo($this->_name);
        } else {
            // Reset connection to ensure reliable transaction
            $this->_client->rset();
        }

        // Set mail return path from sender email address
        $this->_client->mail($this->_mail->getReturnPath());

        // Set recipient forward paths
        foreach ($this->_mail->getRecipients() as $recipient) {
            $this->_client->rcpt($recipient);
        }

        // Issue DATA command to client
        $this->_client->data($this->header . $this->EOL . $this->body);
    }
}
