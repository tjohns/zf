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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * @see Zend_Log
 */
require_once 'Zend/Log.php';

/**
 * @category   Zend
 * @package    Zend_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_Ldap implements Zend_Auth_Adapter_Interface
{

    /**
     * The Zend_Ldap context.
     *
     * @var Zend_Ldap
     */
    protected $_ldap = null;

    /**
     * The array of arrays of Zend_Ldap options passed to the constructor.
     *
     * @var array
     */
    protected $_options = null;

    /**
     * The username of the account being authenticated.
     *
     * @var string
     */
    protected $_username = null;

    /**
     * The password of the account being authenticated.
     *
     * @var string
     */
    protected $_password = null;

    /**
     * @var Zend_Log
     */
    protected $_logger = null;

    /**
     * @param  array  $options  An array of arrays of Zend_Ldap options
     * @param  string $username The username of the account being authenticated
     * @param  string $password The password of the account being authenticated
     * @return void
     */
    public function __construct(array $options = array(), $username = null, $password = null)
    {
        $this->_options = $options;
        if ($username !== null) {
            $this->setUsername($username);
        }
        if ($password !== null) {
            $this->setPassword($password);
        }
    }

    /**
     * Returns the username of the account being authenticated, or
     * NULL if none is set.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @param  string $username The username for binding
     * @return Zend_Auth_Adapter_Ldap Provides a fluent interface
     */
    public function setUsername($username)
    {
        $this->_username = (string) $username;
        return $this;
    }

    /**
     * Returns the password of the account being authenticated, or
     * NULL if none is set.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @param  string $password The password of the account being authenticated
     * @return Zend_Auth_Adapter_Ldap Provides a fluent interface
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }

    /**
     * @return Zend_Ldap The Zend_Ldap object used to authenticate the credentials.
     */
    public function getLdap()
    {
        if ($this->_ldap === null) {
            /**
             * @see Zend_Ldap
             */
            require_once 'Zend/Ldap.php';
            $this->_ldap = new Zend_Ldap();
        }
        return $this->_ldap;
    }

    /**
     * @return Zend_Auth_Result
     * @throws Zend_Auth_Adapter_Exception
     */
    public function authenticate()
    {
        require_once 'Zend/Ldap/Exception.php';

        $username = $this->_username;
        $password = $this->_password;

        if (!$username) {
            $code = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $messages = array('A username is required');
            return new Zend_Auth_Result($code, '', $messages);
        }
        if (!$password) {
            /* A password is required because some servers will
             * treat an empty password as an anonymous bind.
             */
            $code = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $messages = array('A password is required');
            return new Zend_Auth_Result($code, '', $messages);
        }

        $ldap = $this->getLdap();

        $code = Zend_Auth_Result::FAILURE;
        $messages = array("Authority not found: $username");
        $log = $this->getLogger();

        /* Iterate through each server and try to authenticate the supplied
         * credentials against it.
         */
        foreach ($this->_options as $name => $options) {

            if (!is_array($options)) {
                require_once 'Zend/Auth/Adapter/Exception.php';
                throw new Zend_Auth_Adapter_Exception('Adapter options array not in array');
            }

            try {
                $ldap->setOptions($options);
                if ($log)
                    $this->_logOptions($options);

                $canonicalName = $ldap->getCanonicalAccountName($username);

                if (isset($messages[1]))
                    $this->_log($messages[1]);
                $messages = array();

                $ldap->bind($canonicalName, $password);

                $this->_log("$canonicalName authentication successful", Zend_Log::INFO);

                return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $canonicalName);
            } catch (Zend_Ldap_Exception $zle) {

                /* LDAP based authentication is notoriously difficult to diagnose. Therefore
                 * we bend over backwards to capture and log every possible bit of
                 * information when something goes wrong.
                 */

                $err = $zle->getCode();

                if ($err == Zend_Ldap_Exception::LDAP_X_DOMAIN_MISMATCH) {
                    /* This error indicates that the domain supplied in the
                     * username did not match the domains in the server options
                     * and therefore we should just skip to the next set of
                     * server options.
                     */
                    continue;
                } else if ($err == Zend_Ldap_Exception::LDAP_NO_SUCH_OBJECT) {
                    $code = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
                    $messages[] = "Account not found: $username";
                } else if ($err == Zend_Ldap_Exception::LDAP_INVALID_CREDENTIALS) {
                    $code = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                    $messages[] = 'Invalid credentials';
                } else {
                    if ($log) {
                        $str = $zle->getFile() . '(' . $zle->getLine() . '): ' . $zle->getMessage();
                        $this->_log($str, Zend_Log::ERR);
                        $str = str_replace($password, '*****', $zle->getTraceAsString());
                        $this->_log($str, Zend_Log::WARN);
                    }
                    $messages[] = 'An unexpected failure occurred';
                }
                $messages[] = $zle->getMessage();
            }
        }

        $msg = isset($messages[1]) ? $messages[1] : $messages[0];
        $this->_log("$username authentication failed: $msg", Zend_Log::NOTICE);

        return new Zend_Auth_Result($code, $username, $messages);
    }

    /**
     * @param Zend_Log $logger The Zend_Log object that this adapter should use to log debugging information
     * @return Zend_Auth_Adapter_Ldap Provides a fluent interface
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
        return $this;
    }

    /**
     * @return Zend_Log The Zend_Log object used by this adapter to log debugging information
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @param $str The message to be logged
     * @param int The Zend_Log priority value
     * @return Zend_Auth_Adapter_Ldap Provides a fluent interface
     */
    protected function _log($str, $priority = Zend_Log::DEBUG)
    {
        $logger = $this->getLogger();
        if ($logger) {
            $str = 'Ldap: ' . str_replace("\n", "\n  ", $str);
            $logger->log($str, $priority);
        }
        return $this;
    }

    private function _logOptions($options)
    {
        $str = '';
        foreach ($options as $key => $val) {
            if ($str)
                $str .= ',';
            $str .= $key . '=' . $val;
        }
        if ($this->_password)
            $str = str_replace($this->_password, '*****', $str);
        $this->_log($str);
    }
}
