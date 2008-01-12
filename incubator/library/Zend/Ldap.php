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
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Ldap_Exception
 */
require_once 'Zend/Ldap/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap
{

    const ACCTNAME_FORM_DN        = 1;
    const ACCTNAME_FORM_USERNAME  = 2;
    const ACCTNAME_FORM_BACKSLASH = 3;
    const ACCTNAME_FORM_PRINCIPAL = 4;

    /**
     * String used with ldap_connect for error handling purposes.
     *
     * @var string
     */
    private $_connectString;

    /**
     * The raw ldap context resource.
     *
     * @var resource
     */
    protected $_resource = null;

    protected $_host;
    protected $_port;
    protected $_useSsl;
    protected $_username;
    protected $_password;
    protected $_bindRequiresDn;
    protected $_baseDn;
    protected $_accountCanonicalForm;
    protected $_accountDomainName;
    protected $_accountDomainNameShort;
    protected $_accountFilterFormat;

    /**
     * @param string The string to escape.
     * @return string The escaped string
     */
    public static function filterEscape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($si = 0; $si < $len; $si++) {
            $ch = $str[$si];
            $ord = ord($ch);
            if ($ord < 0x20 || $ord > 0x7e || strstr('*()\/', $ch))
                $ch = '\\' . dechex($ord);
            $ret .= $ch;
        }
        return $ret;
    }

    /**
     * @param array $options Options used in connecting, binding, etc.
     */
    public function __construct($options = array())
    {
        if (function_exists('ldap_connect') == false) {
            throw new Zend_Ldap_Exception(null, 'Function ldap_connect not available.');
        }
        $this->setOptions($options);
    }

    /**
     * @param array $options Options used in connecting, binding, etc.
     * @return Zend_Ldap Provides a fluent interface
     * @throws Zend_Ldap_Exception
     */
    public function setOptions($options)
    {
        $permittedOptions = array(
            'host',
            'port',
            'useSsl',
            'username',
            'password',
            'bindRequiresDn',
            'baseDn',
            'accountCanonicalForm',
            'accountDomainName',
            'accountDomainNameShort',
            'accountFilterFormat',
        );

        foreach ($permittedOptions as $key) {
            $member = "_$key";
            $this->$member = null;
            if (isset($options[$key])) {
                $this->$member = $options[$key];
                unset($options[$key]);
            }
        }

        if (empty($options) == false) {
            list($key, $val) = each($options);
            throw new Zend_Ldap_Exception(null, "Unknown Zend_Ldap option: $key");
        }

        /* Account names should always be qualified with a domain. In some scenarios
         * using non-qualified account names can lead to security vulnerbilities. If
         * no account canonical form is specified, we guess based in what domain
         * names have been supplied.
         */
        if (!$this->_accountCanonicalForm) {
            if ($this->_accountDomainNameShort) {
                $this->_accountCanonicalForm = Zend_Ldap::ACCTNAME_FORM_BACKSLASH;
            } else if ($this->_accountDomainName) {
                $this->_accountCanonicalForm = Zend_Ldap::ACCTNAME_FORM_PRINCIPAL;
            } else {
                $this->_accountCanonicalForm = Zend_Ldap::ACCTNAME_FORM_USERNAME;
            }
        }

        return $this;
    }

    /**
     * @return resource The raw LDAP extension resource.
     */
    public function getResource()
    {
        // TODO: by reference?
        return $this->_resource;
    }

    /**
     * @return string The hostname of the LDAP server being used to authenticate accounts
     */
    protected function _getHost()
    {
        return $this->_host;
    }

    /**
     * @return int The port of the LDAP server or 0 to indicate that no port value is set
     */
    protected function _getPort()
    {
        if ($this->_port)
            return $this->_port;
        return 0;
    }

    /**
     * @return string The default acctname for binding
     */
    protected function _getUsername()
    {
        return $this->_username;
    }

    /**
     * @return string The default password for binding
     */
    protected function _getPassword()
    {
        return $this->_password;
    }

    /**
     * @return boolean The default SSL / TLS encrypted transport control
     */
    protected function _getUseSsl()
    {
        return $this->_useSsl;
    }

    /**
     * @return string The default base DN under which objects of interest are located
     */
    protected function _getBaseDn()
    {
        return $this->_baseDn;
    }

    /**
     * @return string A format string for building an LDAP search filter to match an account
     */
    protected function _getAccountFilterFormat()
    {
        return $this->_accountFilterFormat;
    }

    /**
     * @return string The LDAP search filter for matching directory accounts
     */
    protected function _getAccountFilter($acctname)
    {
        $this->_splitName($acctname, $dname, $aname);
        $accountFilterFormat = $this->_getAccountFilterFormat();
        $aname = Zend_Ldap::filterEscape($aname);
        if ($accountFilterFormat)
            return sprintf($accountFilterFormat, $aname);
        if (!$this->_bindRequiresDn) {
            // is there a better way to detect this?
            return "(&(objectClass=user)(sAMAccountName=$aname))";
        }
        return "(&(objectClass=posixAccount)(uid=$aname))";
    }

    /**
     * @param string $name The name to split
     * @param string $dname The resulting domain name (this is an out parameter)
     * @param string $aname The resulting account name (this is an out parameter)
     */
    protected function _splitName($name, &$dname, &$aname)
    {
        $dname = NULL;
        $aname = $name;

        $pos = strpos($name, '@');
        if ($pos) {
            $dname = substr($name, $pos + 1);
            $aname = substr($name, 0, $pos);
        } else {
            $pos = strpos($name, '\\');
            if ($pos) {
                $dname = substr($name, 0, $pos);
                $aname = substr($name, $pos + 1);
            }
        }
    }

    /**
     * @param string $acctname The name of the account
     * @return string The DN of the specified account
     * @throws Zend_Ldap_Exception
     */
    protected function _getAccountDn($acctname)
    {
        if ($this->_isDnString($acctname))
            return $acctname;
        $acctname = $this->getCanonicalAccountName($acctname, Zend_Ldap::ACCTNAME_FORM_USERNAME);
        $acct = $this->_getAccount($acctname, array('dn'));
        return $acct['dn'];
    }

    /**
     * @param string $dname The domain name to check
     * @return bool
     */
    protected function _isPossibleAuthority($dname)
    {
        if ($dname === null)
            return true;
        if ($this->_accountDomainName === null && $this->_accountDomainNameShort === null)
            return true;
        if (strcasecmp($dname, $this->_accountDomainName) == 0)
            return true;
        if (strcasecmp($dname, $this->_accountDomainNameShort) == 0)
            return true;
        return false;
    }

    /**
     * @param string $acctname The name to canonicalize
     * @param int $type The desired form of canonicalization
     * @return string The canonicalized name in the desired form
     * @throws Zend_Ldap_Exception
     */
    public function getCanonicalAccountName($acctname, $form = 0)
    {
        $this->_splitName($acctname, $dname, $uname);

        if (!$this->_isPossibleAuthority($dname)) {
            throw new Zend_Ldap_Exception(null,
                    "Binding domain is not an authority for user: $acctname",
                    Zend_Ldap_Exception::LDAP_X_DOMAIN_MISMATCH);
        }

        if ($form === Zend_Ldap::ACCTNAME_FORM_DN)
            return $this->_getAccountDn($acctname);

        if (!$uname)
            throw new Zend_Ldap_Exception(null, "Invalid account name syntax: $acctname");

        $uname = strtolower($uname);

        if ($form === 0)
            $form = $this->_accountCanonicalForm;

        switch ($form) {
            case Zend_Ldap::ACCTNAME_FORM_USERNAME:
                return $uname;
            case Zend_Ldap::ACCTNAME_FORM_BACKSLASH:
                if (!$this->_accountDomainNameShort)
                    throw new Zend_Ldap_Exception(null, 'Option required: accountDomainNameShort');
                return "$this->_accountDomainNameShort\\$uname";
            case Zend_Ldap::ACCTNAME_FORM_PRINCIPAL:
                if (!$this->_accountDomainName)
                    throw new Zend_Ldap_Exception(null, 'Option required: accountDomainName');
                return "$uname@$this->_accountDomainName";
            default:
                throw new Zend_Ldap_Exception(null, "Unknown canonical name form: $form");
        }
    }

    /**
     * @param array $attrs An array of names of desired attributes
     * @return array An array of the attributes representing the account
     * @throws Zend_Ldap_Exception
     */
    private function _getAccount($acctname, $attrs = null)
    {
        $baseDn = $this->_getBaseDn();
        if (!$baseDn)
            throw new Zend_Ldap_Exception(null, 'Base DN not set');

        $accountFilter = $this->_getAccountFilter($acctname);
        if (!$accountFilter)
            throw new Zend_Ldap_Exception(null, 'Invalid account filter');

        if (!is_resource($this->_resource))
            $this->bind();

        $resource = $this->_resource;
        $str = $accountFilter;
        $code = 0;

        // TODO: break out search operation into simple function (private for now)

        $result = @ldap_search($resource,
                        $baseDn,
                        $accountFilter,
                        $attrs);
        if (is_resource($result) === true) {
            $count = @ldap_count_entries($resource, $result);
            if ($count == 1) {
                $entry = @ldap_first_entry($resource, $result);
                if ($entry) {
                    $acct = array('dn' => @ldap_get_dn($resource, $entry));
                    $name = @ldap_first_attribute($resource, $entry, $berptr);
                    while ($name) {
                        $data = @ldap_get_values_len($resource, $entry, $name);
                        $acct[$name] = $data;
                        $name = @ldap_next_attribute($resource, $entry, $berptr);
                    }
                    @ldap_free_result($result);
                    return $acct;
                }
            } else if ($count == 0) {
                $code = Zend_Ldap_Exception::LDAP_NO_SUCH_OBJECT;
            } else {

                // TODO: limit search to 1 record and remove some of this logic?

                $resource = null;
                $str = "$accountFilter: Unexpected result count: $count";
                $code = Zend_Ldap_Exception::LDAP_OPERATIONS_ERROR;
            }
            @ldap_free_result($result);
        }

        throw new Zend_Ldap_Exception($resource, $str, $code);
    }

    /**
     * @return Zend_Ldap Provides a fluent interface
     */
    public function disconnect()
    {
        if (is_resource($this->_resource))
            @ldap_unbind($this->_resource);
        $this->_resource = null;
        return $this;
    }

    /**
     * @param string $host The hostname of the LDAP server to connect to
     * @param int $port The port number of the LDAP server to connect to
     * @return Zend_Ldap Provides a fluent interface
     * @throws Zend_Ldap_Exception
     */
    public function connect($host = null, $port = 0, $useSsl = false)
    {
        if ($host === null)
            $host = $this->_getHost();
        if ($port === 0)
            $port = $this->_getPort();
        if ($useSsl === false)
            $useSsl = $this->_getUseSsl();

        if (!$host)
            throw new Zend_Ldap_Exception(null, 'A host parameter is required');

        /* To connect using SSL it seems the client tries to verify the server
         * certificate by default. One way to disable this behavior is to set
         * 'TLS_REQCERT never' in OpenLDAP's ldap.conf and restarting Apache. Or,
         * if you really care about the server's cert you can put a cert on the
         * web server.
         */
        $url = $useSsl ? "ldaps://$host" : "ldap://$host";

        /* Because ldap_connect doesn't really try to connect, any connect error
         * will actually occur during the ldap_bind call. Therefore, we save the
         * connect string here for reporting it in error handling in bind().
         */
        $this->_connectString = $url;

        $this->disconnect();

        $resource = $port ? @ldap_connect($url, $port) : @ldap_connect($url);
        if (is_resource($resource) === true) {
            if (@ldap_set_option($resource, LDAP_OPT_PROTOCOL_VERSION, 3) &&
                        @ldap_set_option($resource, LDAP_OPT_REFERRALS, 0)) {
                $this->_resource = $resource;
                return $this;
            }
            throw new Zend_Ldap_Exception($resource, "$host:$port");
        }
        throw new Zend_Ldap_Exception("Failed to connect to LDAP server: $host:$port");
    }

    protected function _isDnString($str)
    {
        return $str && stristr($str, ',DC=');
    }

    /**
     * @param string $acctname The acctname for authenticating the bind
     * @param string $password The password for authenticating the bind
     * @return Zend_Ldap Provides a fluent interface
     * @throws Zend_Ldap_Exception
     */
    public function bind($username = null, $password = null)
    {
        $moreCreds = true;

        if ($username === null) {
            $username = $this->_getUsername();
            $password = $this->_getPassword();
            $moreCreds = false;
        }

        if (!$username)
            throw new Zend_Ldap_Exception(null, 'Cannot determine username for binding');

        /* Check to make sure the username is in DN form.
         */
        if (!$this->_isDnString($username)) {
            if ($this->_bindRequiresDn) {
                /* moreCreds stops an infinite loop if _getUsername does not
                 * return a DN and the bind requires it
                 */
                if ($moreCreds) {
                    try {
                        $username = $this->_getAccountDn($username);
                    } catch (Zend_Ldap_Exception $zle) {
                        switch ($zle->getCode()) {
                            case Zend_Ldap_Exception::LDAP_NO_SUCH_OBJECT:
                            case Zend_Ldap_Exception::LDAP_X_DOMAIN_MISMATCH:
                                throw $zle;
                        }
                        throw new Zend_Ldap_Exception(null,
                                    'Failed to retrieve DN for account: ' . $zle->getMessage(),
                                    Zend_Ldap_Exception::LDAP_OPERATIONS_ERROR);
                    }
                } else {
                    throw new Zend_Ldap_Exception(null, 'Binding requires username in DN form');
                }
            } else {
                $username = $this->getCanonicalAccountName($username,
                            Zend_Ldap::ACCTNAME_FORM_PRINCIPAL);
            }
        }

        if (!is_resource($this->_resource))
            $this->connect();

        if (@ldap_bind($this->_resource, $username, $password))
            return $this;

        $message = $username;

        switch (Zend_Ldap_Exception::getLdapCode($this)) {
            case Zend_Ldap_Exception::LDAP_SERVER_DOWN:
                /* If the error is related to establishing a connection rather than binding,
                 * the connect string is more informative than the username.
                 */
                $message = $this->_connectString;
        }

        $zle = new Zend_Ldap_Exception($this->_resource, $message);
        $this->disconnect();
        throw $zle;
    }
}
