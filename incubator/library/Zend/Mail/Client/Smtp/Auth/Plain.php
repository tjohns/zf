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
class Zend_Mail_Client_Smtp_Auth_Plain extends Zend_Mail_Client_Smtp
{
    protected $_username;
    protected $_password;

    /**
     * Constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $name  (for use with HELO)
     */
    public function __construct($host = '127.0.0.1', $port = null, $config = null)
    {
        if (is_array($config)) {
            if (isset($config['username'])) {
                $this->_username = $config['username'];
            }
            if (isset($config['password'])) {
                $this->_password = $config['password'];
            }
        }
        parent::__construct($host, $port);
    }

    /**
     * Class destructor to cleanup open resources
     *
     */
    public function auth()
    {
        parent::auth();
        
        $this->_send('AUTH PLAIN');
        $this->_expect(334);
        $this->_send(base64_encode(chr(0) . $this->_username . chr(0) . $this->_password));
        $this->_expect(235);
        $this->_auth = true;
    }
}
