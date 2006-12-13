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
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Auth_Adapter
 */
require_once 'Zend/Auth/Adapter.php';


/**
 * Zend
 */
require_once 'Zend.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Digest_Adapter extends Zend_Auth_Adapter
{
    /**
     * Filename against which authentication queries are performed
     *
     * @var string
     */
    protected $_filename;

    /**
     * Creates a new digest authentication object against the $filename provided
     *
     * @param  string $filename
     * @throws Zend_Auth_Digest_Exception
     * @return void
     */
    public function __construct($filename)
    {
        $this->_filename = (string) $filename;
    }

    /**
     * Authenticates against the given parameters
     *
     * @param  string $filename
     * @param  string $realm
     * @param  string $username
     * @param  string $password
     * @throws Zend_Auth_Digest_Exception
     * @return Zend_Auth_Digest_Token
     */
    public static function staticAuthenticate($filename, $realm, $username, $password)
    {
        if (false === ($fileHandle = @fopen($filename, 'r'))) {
            throw Zend::exception('Zend_Auth_Digest_Exception', "Cannot open '$filename' for reading");
        }

        require_once 'Zend/Auth/Digest/Token.php';

        $id       = "$username:$realm";
        $idLength = strlen($id);

        $tokenValid    = false;
        $tokenIdentity = array(
            'realm'    => $realm,
            'username' => $username
            );

        while ($line = fgets($fileHandle)) {
            $line = trim($line);
            if (substr($line, 0, $idLength) === $id) {
                if (substr($line, -32) === md5("$username:$realm:$password")) {
                    $tokenValid   = true;
                    $tokenMessage = null;
                } else {
                    $tokenMessage = 'Password incorrect';
                }
                return new Zend_Auth_Digest_Token($tokenValid, $tokenIdentity, $tokenMessage);
            }
        }

        $tokenMessage = "Username '$username' and realm '$realm' combination not found";
        return new Zend_Auth_Digest_Token($tokenValid, $tokenIdentity, $tokenMessage);
    }

    /**
     * Authenticates the realm, username and password given
     *
     * @param  string $realm
     * @param  string $username
     * @param  string $password
     * @uses   Zend_Auth_Digest_Adapter::staticAuthenticate()
     * @return Zend_Auth_Digest_Token
     */
    public function authenticate($realm, $username, $password)
    {
        return self::staticAuthenticate($this->_filename, $realm, $username, $password);
    }

}
