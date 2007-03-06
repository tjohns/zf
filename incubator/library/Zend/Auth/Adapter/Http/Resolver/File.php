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
 * @copyright  Copyright (c) 2007 Bryce Lohr
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */ 

require_once 'Zend/Auth/Adapter/Http/Resolver/Interface.php';
require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';


/**
 * HTTP Authentication File Resolver
 *
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage Zend_Auth_Adapter_Http_Resolver
 * @copyright  Copyright (c) 2007 Bryce Lohr
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_Http_Resolver_File implements Zend_Auth_Adapter_Http_Resolver_Interface
{
    /**
     * Path to credentials file
     */
    protected $_file;

    
    /**
     * Constructor
     *
     * @param string Complete filename where the credentials are stored
     */
    public function __construct($path = '')
    {
        if (!empty($path)) {
            $this->setFile($path);
        }
    }
    
    /**
     * @param string Path
     * @return void
     * @throws Zend_Auth_Adapter_Http_Resolver_Exception
     */
    public function setFile($path)
    {
        if (empty($path) || !is_readable($path)) {
            throw new Zend_Auth_Adapter_Http_Resolver_Exception('Path not readable: '.$path);
        }
        $this->_file = $path;
    }

    /**
     * @param void
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Resolve credentials
     *
     * Only the first matching username/realm combination in the file is 
     * returned. If the file contains credentials for Digest authentication, 
     * the returned string is the password hash, or h(a1) from RFC 2617. The 
     * returned string is the plain-text password for Basic authentication.
     *
     * The expected format of the file is:
     *   username:realm:sharedSecret
     *
     * That is, each line consists of the user's username, the applicable 
     * authentication realm, and the password or hash, each delimited by 
     * colons.
     * 
     * @param string Username
     * @param string Authentication Realm
     * @return string|false User's shared secret, if the user is found in the
     *         realm, false otherwise.
     * @throws Zend_Auth_Adapter_Http_Resolver_Exception
     */
    public function resolve($username, $realm)
    {
        if (empty($username)) {
            throw new Zend_Auth_Adapter_Http_Resolver_Exception('Username is required');
        } else if (!ctype_print($username) || strpos($username, ':') !== false) {
            throw new Zend_Auth_Adapter_Http_Resolver_Exception('Username must consist only of printable characters, excluding the colon.');
        }
        if (empty($realm)) {
            throw new Zend_Auth_Adapter_Http_Resolver_Exception('Realm is required');
        } else if (!ctype_print($realm) || strpos($realm, ':') !== false) {
            throw new Zend_Auth_Adapter_Http_Resolver_Exception('Realm must consist only of printable characters, excluding the colon.');
        }

        // Open file, read through looking for matching credentials
        $fp = @fopen($this->_file, 'r');
        if (!$fp) {
            throw new Zend_Auth_Adapter_Http_Resolver_Exception('Unable to open password file: '.$this->_file);
        }
        
        // No real validation is done on the contents of the password file. The
        // assumption is that we trust the administrators to keep it secure.
        while (($line = fgetcsv($fp, 512, ':')) !== false) {
            if ($line[0] == $username && $line[1] == $realm) {
                $password = $line[2];
                fclose($fp);
                return $password;
            }
        }

        fclose($fp);    
        return false;
    }
}
