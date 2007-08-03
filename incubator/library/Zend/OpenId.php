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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * @see Zend_OpenId_Exception
 */
require_once "Zend/OpenId/Exception.php";

/**
 * @see Zend_Controller_Response_Abstract
 */
require_once "Zend/Controller/Response/Abstract.php";

/**
  * Default Diffie-Hellman key generator (1024 bit)
  */
define('ZEND_OPEN_ID_DH_P',
       'dcf93a0b883972ec0e19989ac5a2ce310e1d37717e8d9571bb7623731866e61e' .
       'f75a2e27898b057f9891c2e27a639c3f29b60814581cd3b2ca3986d268370557' .
       '7d45c2e7e52dc81c7a171876e5cea74b1448bfdfaf18828efd2519f14e45e382' .
       '6634af1949e5b535cc829a483b8a76223e5d490a257f05bdff16f2fb22c583ab');

/**
 * Static class that contains common utility functions for
 * {@link Zend_OpenId_Consumer} and {@link Zend_OpenId_Provider}.
 *
 * This class implements common utility functions that are used by both
 * Consumer and Provider. They include functions for Diffie-Hellman keys
 * generation and exchange, URL normalization, HTTP redirection and some others.
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Zend_OpenId
{
    /**
     * Default Diffie-Hellman key generator
     */
    const DH_P   = ZEND_OPEN_ID_DH_P;

    /**
     * Default Diffie-Hellman prime number (should be 2 or 5)
     */
    const DH_G   = '02';

    /**
     * OpenID 2.0 namespace. All OpenID 2.0 messages MUST contain variable
     * openid.ns with its value.
     */
    const NS_2_0 = 'http://specs.openid.net/auth/2.0';

    /**
     * Returns a full URL that was requested on current HTTP request.
     *
     * @return string
     */
    static public function selfUrl()
    {
        $port = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            if (strpos($_SERVER['HTTP_HOST'], ':') === false) {
                if (isset($_SERVER['SERVER_PORT'])) {
                    $port = ':' . $_SERVER['SERVER_PORT'];
                }
            }
            $url = $_SERVER['HTTP_HOST'];
        } else if (isset($_SERVER['SERVER_NAME'])) {
            $url = $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['SERVER_PORT'])) {
                $port = ':' . $_SERVER['SERVER_PORT'];
            }
        }
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url = 'https://' . $url;
            if ($port == ':443') {
                $port = '';
            }
        } else {
            $url = 'http://' . $url;
            if ($port == ':80') {
                $port = '';
            }
        }

        $url .= $port;
        if (isset($_SERVER['SCRIPT_URL'])) {
            $url .= $_SERVER['SCRIPT_URL'];
        } else if (isset($_SERVER['SCRIPT_NAME'])) {
            $url .= $_SERVER['SCRIPT_NAME'];
        } else if (isset($_SERVER['PHP_SELF'])) {
            $url .= $_SERVER['PHP_SELF'];
        }
        return $url;
    }

    /**
     * Converts variable/value pairs into URL encoded query string
     *
     * @param array $params - variable/value pairs
     * @return string - URL encoded query string
     */
    static public function paramsToQuery($params)
    {
        foreach($params as $key => $value) {
            if (isset($query)) {
                $query .= '&' . $key . '=' . urlencode($value);
            } else {
                $query = $key . '=' . urlencode($value);
            }
        }
        return isset($query) ? $query : '';
    }

    /**
     * Normalizes URL according to RFC 3986 to use it in comparison operations.
     * The function gets URL argument by reference and modifies it.
     * It returns true on success and false of failure.
     *
     * @param string $id
     * @return bool
     */
    static public function normalizeUrl(&$id)
    {
        $i = 0;
        $n = strlen($id);
        while (($i = strpos($id, '%', $i)) !== false) {
            if ($i+2 < $n &&
                (($s[$i+1] >= '0' && $s[$i+1] <= '9') ||
                 ($s[$i+1] >= 'A' && $s[$i+1] <= 'F') ||
                 ($s[$i+1] >= 'a' && $s[$i+1] <= 'f')) &&
                (($s[$i+2] >= '0' && $s[$i+2] <= '9') ||
                 ($s[$i+2] >= 'A' && $s[$i+2] <= 'F') ||
                 ($s[$i+2] >= 'a' && $s[$i+2] <= 'f'))) {
                if ($s[$i+1] >= 'a' && $s[$i+1] <= 'f') {
                    $s[$i+1] = chr(ord($s[$i+1])-ord('a')+ord('A'));
                }
                if ($s[$i+2] >= 'a' && $s[$i+2] <= 'f') {
                    $s[$i+2] = chr(ord($s[$i+1])-ord('a')+ord('A'));
                }
            }
            $i++;
        }

        // TODO: RFC 3986, 6.2.2.2 Percent-Encoding Normalization

        // 7.2.3
        $url = parse_url($id);
        if ($url === false) {
            return false;
        }
        if (isset($url['scheme'])) {
            $url['scheme'] = strtolower($url['scheme']);
        } else {
            $url['scheme'] = 'http';
        }
        // 7.2.4
        if ($url['scheme'] == 'http') {
            if (isset($url['port']) && $url['port'] == 80) {
                unset($url['port']);
            }
        } else if ($url['scheme'] == 'https') {
            if (isset($url['port']) && $url['port'] == 443) {
                unset($url['port']);
            }
        } else {
            return false;
        }
        if (empty($url['host'])) {
            return false;
        } else {
            $url['host'] = strtolower($url['host']);
        }
        if (empty($url['path'])) {
            $url['path'] = '/';
        }

        // TODO: RFC 3986, 6.2.2.3 Path Segment Normalization

        $id = $url['scheme']
            . '://'
            . $url['host']
            . (empty($url['port']) ? '' : (':' . $url['port']))
            . $url['path']
            . (empty($url['query']) ? '' : ('?' . $url['query']))
            . (empty($url['fragment']) ? '' : ('#' . $url['fragment']));
        return true;
    }

    /**
     * Normalizes OpenID identifier that can be URL or XRI name.
     * Returns true on success and false of failure.
     *
     * Normalization is performed according to the following rules:
     * 1. If the user's input starts with one of the "xri://", "xri://$ip*",
     *    or "xri://$dns*" prefixes, they MUST be stripped off, so that XRIs
     *    are used in the canonical form, and URI-authority XRIs are further
     *    considered URL identifiers.
     * 2. If the first character of the resulting string is an XRI Global
     *    Context Symbol ("=", "@", "+", "$", "!"), then the input SHOULD be
     *    treated as an XRI.
     * 3. Otherwise, the input SHOULD be treated as an http URL; if it does
     *    not include a "http" or "https" scheme, the Identifier MUST be
     *    prefixed with the string "http://".
     * 4. URL identifiers MUST then be further normalized by both following
     *    redirects when retrieving their content and finally applying the
     *    rules in Section 6 of [RFC3986] to the final destination URL.
     * @param string $id
     * @return bool
     */
    static public function normalize(&$id)
    {
        $id = trim($id);
        if (strlen($id) === 0) {
            return true;
        }
        // 7.2.1
        if (strpos($id, 'xri://$ip*') === 0) {
            $id = substr(id, strlen('xri://$ip*'));
        } else if (strpos($id, 'xri://dns*') === 0) {
            $id = substr(id, strlen('xri://dns*'));
        } else if (strpos($id, 'xri://') === 0) {
            $id = substr(id, strlen('xri://'));
        }
        // 7.2.2
        if ($id[0] != '=' &&
            $id[0] != '@' &&
            $id[0] != '+' &&
            $id[0] != '$' &&
            $id[0] != '!') {
            return self::normalizeURL($id);
        }
        return true;
    }

    /**
     * Performs a HTTP redirection to specified URL with additional data.
     * It may generate redirected request using GET or POST HTTP method.
     * The function never returns.
     *
     * @param string $url URL to redirect to
     * @param array $params additional variable/value pairs to send
     * @param Zend_Controller_Response_Abstract $response
     * @param string $method redirection method ('GET' or 'POST')
     */
    static public function redirect($url, $params = null, 
        Zend_Controller_Response_Abstract $response = null, $method = 'GET')
    {
        if (null === $response) {
            require_once "Zend/Controller/Response/Http.php";
            $response = new Zend_Controller_Response_Http();
        }
        
        if (is_array($params) && count($params) > 0) {
            if (strpos($url, '?') === false) {
                $url .= '?' . self::paramsToQuery($params);
            } else {
                $url .= '&' . self::paramsToQuery($params);
            }
        }
        if (!$response->canSendHeaders()) {
            $response->setBody("<script language=\"JavaScript\"" .
                 " type=\"text/javascript\">window.location='$url';" .
                 "</script>");
        } else {
            $response->setRedirect($url);
        }
        $response->sendResponse();
        exit();
    }

    /**
     * Produces string of random byte of given length.
     *
     * @param integer $len length of requested string
     * @return string RAW random binary string
     */
    static public function randomBytes($len)
    {
        $key = '';
        for($i=0; $i < $len; $i++) {
            $key .= chr(mt_rand(0, 255));
        }
        return $key;
    }

    /**
     * Generates a hash value (message digest) according to given algorithm.
     * It returns RAW binary string.
     *
     * This is a wrapper function that uses one of available internal function
     * dependent on given PHP configuration. It may use various functions from
     *  ext/openssl, ext/hash, ext/mhash or ext/standard.
     *
     * @param string $func digest algorithm
     * @param string $data data to sign
     * @return string RAW digital signature
     * @throws Zend_OpenId_Exception
     */
    static public function digest($func, $data)
    {
        if (function_exists('openssl_digest')) {
            return openssl_digest($data, $func, true);
        } else if (function_exists('hash')) {
            return hash($func, $data, true);
        } else if ($func === 'sha1') {
            return sha1($data, true);
        } else if ($func = 'sha256') {
            if (function_exists('mhash')) {
                return mhash(MHASH_SHA256 , $data);
            }
        }
        throw new Zend_OpenId_Exception(
            'Unsupported digest algorithm "' . $func . '".',
            Zend_OpenId_Exception::UNSUPPORTED_DIGEST);
    }

    /**
     * Generates a keyed hash value using the HMAC method. It uses ext/hash
     * if available or user-level PHP implementation, that is not significantly
     * slower.
     *
     * @param string $macFunc name of selected hashing algorithm (sha1, sha256)
     * @param string $data data to sign
     * @param string $secret shared secret key used for generating the HMAC
     *  variant of the message digest
     * @return string RAW HMAC value
     */
    static public function hashHmac($macFunc, $data, $secret)
    {
        if (function_exists('hash_hmac')) {
            return hash_hmac($macFunc, $data, $secret, 1);
        } else {
            if (strlen($secret) > 64) {
                $secret = self::digest($macFunc, $secret);
            }
            $secret = str_pad($secret, 64, chr(0x00));
            $ipad = str_repeat(chr(0x36), 64);
            $opad = str_repeat(chr(0x5c), 64);
            $hash1 = self::digest($macFunc, ($secret ^ $ipad) . $data);
            return self::digest($macFunc, ($secret ^ $opad) . $hash1);
        }
    }

    /**
     * Converts binary representation into ext/gmp or ext/bcmath big integer
     * representation.
     *
     * @param string $bin binary representation of big number
     * @return mixed
     * @throws Zend_OpenId_Exception
     */
    static public function binToBigNum($bin)
    {
        if (extension_loaded('gmp')) {
            return gmp_init(bin2hex($bin), 16);
        } else if (extension_loaded('bcmath')) {
            $bn = 0;
            $len = strlen($bin);
            for ($i = 0; $i < $len; $i++) {
                $bn = bcmul($bn, 256);
                $bn = bcadd($bn, ord($bin[$i]));
            }
            return $bn;
        }
        throw new Zend_OpenId_Exception(
            'The system doesn\'t have proper big integer extension',
            Zend_OpenId_Exception::UNSUPPORTED_LONG_MATH);
    }

    /**
     * Converts internal ext/gmp or ext/bcmath big integer representation into
     * binary string.
     *
     * @param mixed $bn big number
     * @return string
     * @throws Zend_OpenId_Exception
     */
    static public function bigNumToBin($bn)
    {
        if (extension_loaded('gmp')) {
            return pack("H*", gmp_strval($bn, 16));
        } else if (extension_loaded('bcmath')) {
            $cmp = bccomp($bn, 0);
            if ($cmp == 0) {
                return (chr(0));
            } else if ($cmp < 0) {
                throw new Zend_OpenId_Exception(
                    'Big integer arithmetic error',
                    Zend_OpenId_Exception::ERROR_LONG_MATH);
            }
            $bin = "";
            while (bccomp($bn, 0) > 0) {
                $bin = chr(bcmod($bn, 256)) . $bin;
                $bn = bcdiv($bn, 256);
            }
            return $bin;
        }
        throw new Zend_OpenId_Exception(
            'The system doesn\'t have proper big integer extension',
            Zend_OpenId_Exception::UNSUPPORTED_LONG_MATH);
    }

    /**
     * Performs the first step of a Diffie-Hellman key exchange by generating
     * private and public DH values based on given prime number $p and
     * generator $g. Both sides of key exchange MUST have the same prime number
     * and generator. In this case they will able to create a random shared
     * secret that is never send from one to the other.
     *
     * @param string $p prime number in binary representation
     * @param string $g generator in binary representation
     * @return mixed
     */
    static public function createDhKey($p, $g)
    {
        if (function_exists('openssl_dh_compute_key')) {
            return openssl_pkey_new(
                array(
                    'dh' => array(
                        'p' => $p,
                        'g' => $g,
                    )
                )
            );
        } else {
            $bn_p        = self::binToBigNum($p);
            $bn_g        = self::binToBigNum($g);
            $priv_key    = self::randomBytes(strlen($p));
            $bn_priv_key = self::binToBigNum($priv_key);
            if (extension_loaded('gmp')) {
                $bn_pub_key  = gmp_powm($bn_g, $bn_priv_key, $bn_p);
            } else if (extension_loaded('bcmath')) {
                $bn_pub_key  = bcpowmod($bn_g, $bn_priv_key, $bn_p);
            }
            $pub_key     = self::bigNumToBin($bn_pub_key);

            return array(
                'p'        => $bn_p,
                'g'        => $bn_g,
                'priv_key' => $bn_priv_key,
                'pub_key'  => $bn_pub_key,
                'details'  => array(
                    'p'        => $p,
                    'g'        => $g,
                    'priv_key' => $priv_key,
                    'pub_key'  => $pub_key));
        }
    }

    /**
     * Returns an associative array with Diffie-Hellman key components in
     * binary representation. The array includes original prime number 'p' and
     * generator 'g', random private key 'priv_key' and corresponding public
     * key 'pub_key'.
     *
     * @param mixed $dh Diffie-Hellman key
     * @return array
     */
    static public function getDhKeyDetails($dh)
    {
        if (function_exists('openssl_dh_compute_key')) {
            $details = openssl_pkey_get_details($dh);
            if (isset($details['dh'])) {
                return $details['dh'];
            }
        } else {
            return $dh['details'];
        }
    }

    /**
     * Computes the shared secret from the private DH value $dh and the other
     * party's public value in $pub_key
     *
     * @param string $pub_key other party's public value
     * @param mixed $dh Diffie-Hellman key
     * @return string
     * @throws Zend_OpenId_Exception
     */
    static public function computeDhSecret($pub_key, $dh)
    {
        if (function_exists('openssl_dh_compute_key')) {
            $ret = openssl_dh_compute_key($pub_key, $dh);
            return $ret;
        } else if (extension_loaded('gmp')) {
            $bn_pub_key = self::binToBigNum($pub_key);
            $bn_secret  = gmp_powm($bn_pub_key, $dh['priv_key'], $dh['p']);
            return self::bigNumToBin($bn_secret);
        } else if (extension_loaded('bcmath')) {
            $bn_pub_key = self::binToBigNum($pub_key);
            $bn_secret  = bcpowmod($bn_pub_key, $dh['priv_key'], $dh['p']);
            return self::bigNumToBin($bn_secret);
        }
        throw new Zend_OpenId_Exception(
            'The system doesn\'t have proper big integer extension',
            Zend_OpenId_Exception::UNSUPPORTED_LONG_MATH);
    }

    /**
     * Takes an arbitrary precision integer and returns its shortest big-endian
     * two's complement representation.
     *
     * Arbitrary precision integers MUST be encoded as big-endian signed two's
     * complement binary strings. Henceforth, "btwoc" is a function that takes
     * an arbitrary precision integer and returns its shortest big-endian two's
     * complement representation. All integers that are used with
     * Diffie-Hellman Key Exchange are positive. This means that the left-most
     * bit of the two's complement representation MUST be zero. If it is not,
     * implementations MUST add a zero byte at the front of the string.
     *
     * @param string $str binary representation of arbitrary precision integer
     * @return string big-endian signed representation
     */
    static public function btwoc($str)
    {
        if (ord($str[0]) > 127) {
            return chr(0) . $str;
        }
        return $str;
    }

}
