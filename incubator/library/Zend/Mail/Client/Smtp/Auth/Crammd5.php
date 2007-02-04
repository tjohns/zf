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
 * Zend_Mail_Client_Smtp
 */
require_once 'Zend/Mail/Client/Smtp.php';


/**
 * Zend_Mail_Client_Exception
 */
require_once 'Zend/Mail/Client/Exception.php';


/**
 * Performs CRAM-MD5 authentication
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Client_Smtp_Auth_Crammd5 extends Zend_Mail_Client_Smtp
{
    /**
     * Constructor.
     *
     * @param string $host   (Default: 127.0.0.1)
     * @param int    $port   (Default: null)
     * @param array  $config Auth-specific parameters
     * @todo Parse $config with Auth-specific parameters
     */
    public function __construct($host = '127.0.0.1', $port = null, $config = null)
    {
        parent::__construct($host, $port);
    }

    
    /**
     * @todo Perform CRAM-MD5 authentication with supplied credentials
     */
    public function auth()
    {
        throw new Zend_Mail_Client_Exception('CRAM-MD5 Not yet implemented.');
    }
}
