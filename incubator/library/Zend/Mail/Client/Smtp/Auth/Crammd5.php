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
class Zend_Mail_Transport_Smtp_Auth_Crammd5
{
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
    public function __construct(Zend_Mail_Client_Smtp $client)
    {
        $this->_client = $client;
    }

    /**
     * Class destructor to cleanup open resources
     *
     */
    public function authenticate($username, $password)
    {
        $this->_client->auth('CRAM-MD5', array(base64_encode($username), base64_encode($password)));
    }
}
