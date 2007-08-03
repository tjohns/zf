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
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Consumer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * @see Zend_OpenId
 */
require_once "Zend/OpenId.php";

/**
 * @see Zend_OpenId_Extension
 */
require_once "Zend/OpenId/Extension.php";

/**
 * OpenID consumer implementation
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Consumer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_OpenId_Consumer
{

    /**
     * Reference to an implementation of storage object
     *
     * @var Zend_OpenId_Consumer_Storage $_storage
     */
    private $_storage = null;

    /**
     * Enables or disables consumer to use association with server based on
     * Diffie-Hellman key agreement
     *
     * @var Zend_OpenId_Consumer_Storage $_dumbMode
     */
    private $_dumbMode = false;

    /**
     * Internal cache to prevent unnecessary access to storage
     *
     * @var array $_cache
     */
    private $_cache = array();

    /**
     * Constructs a Zend_OpenId_Consumer object with given $storage.
     * Enables or disables future association with server based on
     * Diffie-Hellman key agreement.
     *
     * @param Zend_OpenId_Consumer_Storage $storage
     * @param bool $dumpMode
     */
    public function __construct(Zend_OpenId_Consumer_Storage $storage = null,
                                $dumbMode = false)
    {
        if ($storage === null) {
            require_once "Zend/OpenId/Consumer/Storage/File.php";
            $this->_storage = new Zend_OpenId_Consumer_Storage_File();
        } else {
            $this->_storage = $storage;
        }
        $this->_dumbMode = $dumbMode;
    }

    /**
     * Performs check (with possible user interaction) of OpenID identity.
     *
     * This is the first step of OpenID authentication process.
     * On success the function does not return (it does HTTP redirection to
     * server and exits). On failure it returns false.
     *
     * @param string $id OpenID identity
     * @param string $returnTo URL to redirect response from server to
     * @param string $root HTTP URL to identify consumer on server
     * @param mixed $extensions extension object or array of extensions objects
     * @param Zend_Controller_Response_Abstract $response
     * @return bool
     */
    public function login($id, $returnTo=null, $root=null, $extensions=null,
                          Zend_Controller_Response_Abstract $response = null)
    {
        return $this->_checkId(
            false,
            $id,
            $returnTo,
            $root,
            $extensions,
            $response);
    }

    /**
     * Performs immediate check (without user interaction) of OpenID identity.
     *
     * This is the first step of OpenID authentication process.
     * On success the function does not return (it does HTTP redirection to
     * server and exits). On failure it returns false.
     *
     * @param string $id OpenID identity
     * @param string $returnTo HTTP URL to redirect response from server to
     * @param string $root HTTP URL to identify consumer on server
     * @param mixed $extensions extension object or array of extensions objects
     * @param Zend_Controller_Response_Abstract $response
     * @return bool
     */
    public function check($id, $returnTo=null, $root=null, $extensions,
                          Zend_Controller_Response_Abstract $response = null)

    {
        return $this->_checkId(
            true,
            $id,
            $returnTo,
            $root,
            $extensions,
            $response);
    }

    /**
     * Verifies authentication response from OpenID server.
     *
     * This is the second step of OpenID authentication process.
     * The function returns true on successful authentication and false on
     * failure.
     *
     * @param array $params HTTP query data from OpenID server
     * @return bool
     */
    public function verify($params)
    {
        if (empty($params['openid_return_to']) ||
            empty($params['openid_signed']) ||
            empty($params['openid_sig']) ||
            $params['openid_return_to'] != Zend_OpenId::selfUrl()) {
            return false;
        }
        if (empty($params['openid_assoc_handle'])) {
            return false;
        }

        if ($this->_storage->getAssociationByHandle(
                $params['openid_assoc_handle'],
                $url,
                $macFunc,
                $secret,
                $expires)) {
            $signed = explode(',', $params['openid_signed']);
            $data = '';
            foreach ($signed as $key) {
                $data .= $key . ':' . $params['openid_' . strtr($key,'.','_')] . "\n";
            }
//$f = fopen("php://stderr", "w");
//fprintf($f, "-verify\n");
//fprintf($f, "data   = %s\n", var_export($data,1));
//fprintf($f, "handle = %s\n", $params['openid_assoc_handle']);
//fprintf($f, "secret = %s\n", bin2hex($secret));
//fprintf($f, "hmac_s = %s\n", bin2hex(base64_decode($params['openid_sig'])));
//fprintf($f, "hmac_c = %s\n", bin2hex(Zend_OpenId::hashHmac($macFunc, $data, $secret)));

            if (base64_decode($params['openid_sig']) ==
                Zend_OpenId::hashHmac($macFunc, $data, $secret)) {
                return true;
            }

            $this->_storage->delAssociation($url);
            return false;
        }
        else
        {
            /* Use dumb mode */
            $id = @$params['openid_identity'];
            if (empty($id) ||
                !$this->_discovery($id, $server, $version)) {
                return false;
            }
            $params2 = array();
            foreach ($params as $key => $val) {
                if (strpos($key, 'openid_ns_') === 0) {
                    $key = 'openid.ns.' . substr($key, strlen('openid_ns_'));
                } else if (strpos($key, 'openid_sreg_') === 0) {
                    $key = 'openid.sreg.' . substr($key, strlen('openid_sreg_'));
                } else if (strpos($key, 'openid_') === 0) {
                    $key = 'openid.' . substr($key, strlen('openid_'));
                }
                $params2[$key] = $val;
            }
            $params2['openid.mode'] = 'check_authentication';
            $ret = $this->_httpRequest($server, 'POST', $params2);
            $i = strpos($ret, "is_valid:true\n");
            if ($i !== false && ($i == 0 || $ret[$i-1] == "\n")) {
                return true;
            }
            return false;
        }
    }

    /**
     * Store assiciation in internal chace and external storage
     *
     * @param string $url OpenID server url
     * @param string $handle association handle
     * @param string $macFunc HMAC function (sha1 or sha256)
     * @param string $secret shared secret
     * @param integer $expires expiration UNIX time
     * @return void
     */
    protected function _addAssociation($url, $handle, $macFunc, $secret, $expires)
    {
        $this->_cache[$url] = array($handle, $macFunc, $secret, $expires);
        return $this->_storage->addAssociation(
            $url,
            $handle,
            $macFunc,
            $secret,
            $expires);
    }

    /**
     * Retrive assiciation information for given $url from internal cahce or
     * external storage
     *
     * @param string $url OpenID server url
     * @param string $handle association handle
     * @param string $macFunc HMAC function (sha1 or sha256)
     * @param string $secret shared secret
     * @param integer $expires expiration UNIX time
     * @return void
     */
    protected function _getAssociation($url, &$handle, &$macFunc, &$secret, &$expires)
    {
        if (isset($this->_cache[$url])) {
            $handle   = $this->_cache[$url][0];
            $macFunc = $this->_cache[$url][1];
            $secret   = $this->_cache[$url][2];
            $expires  = $this->_cache[$url][3];
            return true;
        }
        if ($this->_storage->getAssociation(
                $url,
                $handle,
                $macFunc,
                $secret,
                $expires)) {
            $this->_cache[$url] = array($handle, $macFunc, $secret, $expires);
            return true;
        }
        return false;
    }

    /**
     * Performs HTTP request to given $url using given HTTP $method.
     * Send additinal query specified by variable/value array,
     * On success returns HTTP response without headers, false on failure.
     *
     * @param string $url OpenID server url
     * @param string $method
     * @param array $params
     * @return mixed
     */
    protected function _httpRequest($url, $method = 'GET', array $params = array())
    {
        require_once 'Zend/Http/Client.php';

        $client = new Zend_Http_Client(
                $url,
                array(
                    'maxredirects' => 4,
                    'timeout'      => 15,
                    'useragent'    => 'Zend_OpenId'
                )
            );

        if ($method == 'POST') {
            $client->setMethod(Zend_Http_Client::POST);
            if (count($params) > 0) {
                $client->setParameterPost($params);
            }
        } else if (count($params) > 0) {
            $client->setParameterGet($params);
        }

        $response = $client->request();
        if ($response->getStatus() == 200) {
            return $response->getBody();
        }else{
            return false;
        }
    }

    /**
     * Create (or reuse existing) association between OpenID consumer and
     * OpenID server based on Diffie-Hellman key agreement. Returns true
     * on success and false on failure.
     *
     * @param string $url OpenID server url
     * @param float $version OpenID protocol version
     * @return bool
     */
    protected function _associate($url, $version)
    {

        /* Check if we already have association in chace or storage */
        if ($this->_getAssociation(
                $url,
                $handle,
                $macFunc,
                $secret,
                $expires)) {
            return true;
        }

        if ($this->_dumbMode) {
            /* Use dumb mode */
            return true;
        }

        $params = array();

        if ($version >= 2.0) {
            $params = array(
                'openid.ns'           => Zend_OpenId::NS_2_0,
                'openid.mode'         => 'associate',
                'openid.assoc_type'   => 'HMAC-SHA256',
                'openid.session_type' => 'DH-SHA256',
            );
        } else {
            $params = array(
                'openid.mode'         => 'associate',
                'openid.assoc_type'   => 'HMAC-SHA1',
                'openid.session_type' => 'DH-SHA1',
            );
        }


        if (!empty($params['openid.session_type']) &&
            ($params['openid.session_type'] !== 'no-encryption')) {

            $dh = Zend_OpenId::createDhKey(pack('H*', Zend_OpenId::DH_P),
                                           pack('H*', Zend_OpenId::DH_G));
            $dh_details = Zend_OpenId::getDhKeyDetails($dh);

            $params['openid.dh_modulus']         =
                base64_encode(Zend_OpenId::btwoc($dh_details['p']));
            $params['openid.dh_gen']             =
                base64_encode(Zend_OpenId::btwoc($dh_details['g']));
            $params['openid.dh_consumer_public'] =
                base64_encode(Zend_OpenId::btwoc($dh_details['pub_key']));
        } else {
            if ($version >= 2.0) {
                if (empty($params['openid.session_type'])) {
                    $params['openid.session_type'] = 'no-encryption';
                }
            } else {
                if ($params['openid.session_type'] === 'no-encryption') {
                    $params['openid.session_type'] = '';
                }

            }
        }

        $ret = $this->_httpRequest($url, 'POST', $params);
        if ($ret === false) {
            return false;
        }

        $r = array();
        foreach(explode("\n", $ret) as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $x = explode(':', $line, 2);
                if (is_array($x) && count($x) == 2) {
                    list($key, $value) = $x;
                    $r[trim($key)] = trim($value);
                }
            }
        }
        $ret = $r;

        if ($version >= 2.0 &&
            isset($ret['ns']) &&
            $ret['ns'] != Zend_OpenId::NS_2_0) {
            return false;
        }

        if (!isset($ret['assoc_handle']) ||
            !isset($ret['expires_in']) ||
            !isset($ret['assoc_type']) ||
            ($params['openid.assoc_type'] != $ret['assoc_type']) ||
            (isset($ret['session_type']) &&
             $ret['session_type'] != $params['openid.session_type']) ||
            (!isset($ret['session_type']) &&
             !empty($params['openid.session_type']))) {
            return false;
        }

        $handle     = $ret['assoc_handle'];
        $expiresIn = $ret['expires_in'];

        if ($ret['assoc_type'] == 'HMAC-SHA1') {
            $macFunc = 'sha1';
        } else if ($ret['assoc_type'] == 'HMAC-SHA256' &&
            $version >= 2.0) {
            $macFunc = 'sha256';
        } else {
            return false;
        }

        if ((($version < 2.0 &&
              empty($ret['session_type'])) ||
             ($version >= 2.0 &&
              isset($ret['session_type']) &&
              $ret['session_type'] == 'no-encryption')) &&
             isset($ret['mac_key'])) {
            $secret = base64_decode($ret['mac_key']);
        } else if (isset($ret['session_type']) &&
            $ret['session_type'] == 'DH-SHA1' &&
            !empty($ret['dh_server_public']) &&
            !empty($ret['enc_mac_key'])) {
            $dhFunc = 'sha1';
        } else if (isset($ret['session_type']) &&
            $ret['session_type'] == 'DH-SHA256' &&
            $version >= 2.0 &&
            !empty($ret['dh_server_public']) &&
            !empty($ret['enc_mac_key'])) {
            $dhFunc = 'sha256';
        } else {
            return false;
        }
        if (isset($dhFunc)) {
            $serverPub = base64_decode($ret['dh_server_public']);
            $dhSec = Zend_OpenId::computeDhSecret($serverPub, $dh);
            if ($dhSec === false) {
                return false;
            }
            $sec = Zend_OpenId::digest($dhFunc, $dhSec);
            if ($sec === false) {
                return false;
            }
            $secret = $sec ^ base64_decode($ret['enc_mac_key']);
        }
        $this->_addAssociation(
            $url,
            $handle,
            $macFunc,
            $secret,
            time() + $expiresIn);

//$f = fopen("php://stderr", "w");
//fprintf($f, "-associate\n");
//fprintf($f, "p        = %s\n", bin2hex($dh_details['p']));
//fprintf($f, "g        = %s\n", bin2hex($dh_details['g']));
//fprintf($f, "priv_key = %s\n", bin2hex($dh_details['priv_key']));
//fprintf($f, "pub_key  = %s\n", bin2hex($dh_details['pub_key']));
//fprintf($f, "spub_key = %s\n", bin2hex(base64_decode($ret['dh_server_public'])));
//fprintf($f, "dhSec    = %s\n", bin2hex($dhSec));
//fprintf($f, "sec      = %s\n", bin2hex($sec));
//fprintf($f, "enc_mac  = %s\n", bin2hex(base64_decode($ret['enc_mac_key'])));
//fprintf($f, "secret   = %s\n", bin2hex($secret));

        return true;
    }

    /**
     * Performs discovery of identity and finds OpenID URL, OpenID server URL
     * and OpenID protocol version. Returns true on succees and false on
     * failure.
     *
     * @param string $id OpenID identity URL
     * @param string $server OpenID server URL
     * @param float $version OpenID protocol version
     * @return bool
     */
    protected function _discovery(&$id, &$server, &$version)
    {
        $realId = $id;
        if ($this->_storage->getDiscoveryInfo(
                $id,
                $realId,
                $server,
                $version)) {
            $id = $realId;
            return true;
        }
        $response = $this->_httpRequest($id);
        if (!is_string($response)) {
            return false;
        }
        if (preg_match(
                '/<link[^>]*rel="openid2.provider"[^>]*href="([^"]+)"[^>]*\/?>/i',
                $response,
                $r) ||
            preg_match(
                '/<link[^>]*href="([^"]+)"[^>]*rel="openid2.provider"[^>]*\/?>/i',
                 $response,
                 $r)) {
            $version = 2.0;
        } else {
            if (!preg_match(
                    '/<link[^>]*rel="openid.server"[^>]*href="([^"]+)"[^>]*\/?>/i',
                    $response,
                    $r) &&
                !preg_match(
                    '/<link[^>]*href="([^"]+)"[^>]*rel="openid.server"[^>]*\/?>/i',
                    $response,
                    $r)) {
                return false;
            }
            $version = 1.0;
        }
        $server = $r[1];
        if ($version >= 2.0) {
            if (preg_match(
                    '/<link[^>]*rel="openid2.local_id"[^>]*href="([^"]+)"[^>]*\/?>/i',
                    $response,
                    $r) ||
                preg_match(
                    '/<link[^>]*href="([^"]+)"[^>]*rel="openid2.local_id"[^>]*\/?>/i',
                    $response,
                    $r)) {
                $realId = $r[1];
            }
        } else {
            if (preg_match(
                    '/<link[^>]*rel="openid.delegate"[^>]*href="([^"]+)"[^>]*\/?>/i',
                    $response,
                    $r) ||
                preg_match(
                    '/<link[^>]*href="([^"]+)"[^>]*rel="openid.delegate"[^>]*\/?>/i',
                    $response,
                    $r)) {
                $realId = $r[1];
            }
        }

        $this->_storage->addDiscoveryInfo($id, $realId, $server, $version);
        $id = $realId;
        return true;
    }

    /*
     * Performs check of OpenID identity.
     *
     * This is the first step of OpenID authentication process.
     * On success the function does not return (it does HTTP redirection to
     * server and exits). On failure it returns false.
     *
     * @param bool $immediate enables or disables interaction with user
     * @param string $id OpenID identity
     * @param string $returnTo HTTP URL to redirect response from server to
     * @param string $root HTTP URL to identify consumer on server
     * @param mixed $extensions extension object or array of extensions objects
     * @param Zend_Controller_Response_Abstract $response
     * @return bool
     */
    protected function _checkId($immediate, $id, $returnTo=null, $root=null,
        $extensions=null, Zend_Controller_Response_Abstract $response = null)
    {
        if (!Zend_OpenId::normalize($id)) {
            return false;
        }
        $climedId = $id;

        if (!$this->_discovery($id, $server, $version)) {
            return false;
        }
        if (!$this->_associate($server, $version)) {
            return false;
        }
        if (!$this->_getAssociation(
                $server,
                $handle,
                $macFunc,
                $secret,
                $expires)) {
            /* Use dumb mode */
            unset($handle);
            unset($macFunc);
            unset($secret);
            unset($expires);
        }

        $params = array();
        if ($version >= 2.0) {
            $params['openid.ns'] = Zend_OpenId::NS_2_0;
        }

        $params['openid.mode'] =
            $immediate ? 'checkid_immediate' : 'checkid_setup';

        $params['openid.identity'] = $id;

        $params['openid.claimed_id'] = $climedId;

        if (isset($handle)) {
            $params['openid.assoc_handle'] = $handle;
        }

        if (empty($returnTo)) {
            $returnTo = Zend_OpenId::selfUrl();
        }
        $params['openid.return_to'] = $returnTo;

        if (empty($root)) {
            $root = dirname(Zend_OpenId::selfUrl());
        }
        if ($version >= 2.0) {
            $params['openid.realm'] = $root;
        } else {
            $params['openid.trust_root'] = $root;
        }

        if (!Zend_OpenId_Extension::forAll($extensions, 'prepareRequest', $params)) {
            return false;
        }

        Zend_OpenId::redirect($server, $params, $response);
    }
}
