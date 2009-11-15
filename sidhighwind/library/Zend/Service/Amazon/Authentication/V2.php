<?php

require_once 'Zend/Service/Amazon/Authentication.php';

/**
 * @see Zend_Crypt_Hmac
 */
require_once 'Zend/Crypt/Hmac.php';

class Zend_Service_Amazon_Authentication_V2 extends Zend_Service_Amazon_Authentication
{
    /**
     * Signature Version
     */
    protected $_signatureVersion = '2';

    /**
     * Signature Encoding Method
     */
    protected $_signatureMethod = 'HmacSHA256';

    public function generateSignature($url, array &$parameters)
    {
        $parameters['AWSAccessKeyId']   = $this->_accessKey;
        $parameters['SignatureVersion'] = $this->_signatureVersion;
        $parameters['Version']          = $this->_apiVersion;
        $parameters['SignatureMethod']  = $this->_signatureMethod;
        if(!isset($parameters['Timestamp'])) {
            $parameters['Timestamp']    = gmdate('Y-m-d\TH:i:s\Z', time()+10);
        }

        $data = $this->_signParameters($url, $parameters);

        return $data;
    }


	/**
     * Adds required authentication and version parameters to an array of
     * parameters
     *
     * The required parameters are:
     * - AWSAccessKey
     * - SignatureVersion
     * - Timestamp
     * - Version and
     * - Signature
     *
     * If a required parameter is already set in the <tt>$parameters</tt> array,
     * it is overwritten.
     *
     * @param  string $queue_url  Queue URL
     * @param  array  $parameters the array to which to add the required
     *                            parameters.
     * @return array
     */
    /*protected function addRequiredParameters($queue_url, array $parameters)
    {
        $parameters['AWSAccessKeyId']   = $this->_getAccessKey();
        $parameters['SignatureVersion'] = $this->_signatureVersion;
        $parameters['Timestamp']        = gmdate('Y-m-d\TH:i:s\Z', time()+10);
        $parameters['Version']          = $this->_sqsApiVersion;
        $parameters['SignatureMethod']  = $this->_signatureMethod;
        $parameters['Signature']        = $this->_signParameters($queue_url, $parameters);

        return $parameters;
    }*/

    /**
     * Computes the RFC 2104-compliant HMAC signature for request parameters
     *
     * This implements the Amazon Web Services signature, as per the following
     * specification:
     *
     * 1. Sort all request parameters (including <tt>SignatureVersion</tt> and
     *    excluding <tt>Signature</tt>, the value of which is being created),
     *    ignoring case.
     *
     * 2. Iterate over the sorted list and append the parameter name (in its
     *    original case) and then its value. Do not URL-encode the parameter
     *    values before constructing this string. Do not use any separator
     *    characters when appending strings.
     *
     * @param  string $queue_url  Queue URL
     * @param  array  $parameters the parameters for which to get the signature.
     *
     * @return string the signed data.
     */
    protected function _signParameters($url, array &$paramaters)
    {
        $data = "GET\n";
        $data .= parse_url($url, PHP_URL_HOST) . "\n";
        $data .= ('' == $path = parse_url($url, PHP_URL_PATH)) ? '/' : $path;
        $data .= "\n";

        uksort($paramaters, 'strcmp');
        unset($paramaters['Signature']);

        $arrData = array();
        foreach($paramaters as $key => $value) {
            $arrData[] = $key . '=' . str_replace('%7E', '~', rawurlencode($value));
        }

        $data .= implode('&', $arrData);

        $hmac = Zend_Crypt_Hmac::compute($this->_secretKey, 'SHA256', $data, Zend_Crypt_Hmac::BINARY);

        $paramaters['Signature'] = base64_encode($hmac);

        return $data;
    }

}