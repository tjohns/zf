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
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Service_StrikeIron_Exception */
require_once 'Zend/Service/StrikeIron/Exception.php';

/** Zend_Service_StrikeIron_ResultDecorator */
require_once 'Zend/Service/StrikeIron/ResultDecorator.php';
 
/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_Service_StrikeIron_Base
{
    /**
     * URL to WSDL for the remote service
     * @var string
     */
    protected $_wsdl;
    
    /**
     * Username for StrikeIron services
     * @var string
     */
    protected $_username;
     
    /**
     * Password for StrikeIron services
     * @var string
     */
    protected $_password;
     
    /** 
     * Headers to pass to SOAPClient->__soapCall()
     * @var mixed
     */
    protected $_soapHeaders;
     
    /**
     * SOAPClient instance or equivalent
     * @var SOAPClient|object
     */
    protected $_soapClient;

    /**
     * Decorated subscription status returned in SOAP headers
     * @var null|Zend_Service_StrikeIron_ResultDecorator
     */
    protected $_subscriptionInfo;

    /**
     * Class constructor
     *
     * @param string       $username     Username for StrikeIron services
     * @param string       $password     Password for StrikeIron services
     * @param mixed        $soapHeaders  Headers to pass to SOAPClient->__soapCall()
     * @param mixed        $soapClient   SOAPClient instance or equivalent
     * @param string|null  $wsdl         URL to WSDL for this service
     */     
    public function __construct($username, $password, $soapHeaders = null, $soapClient = null, $wsdl = null)
    {
        $this->_username = $username;
        $this->_password = $password;

        if (isset($wsdl)) {
            $this->_wsdl = $wsdl;
        }

        $this->_initSoapHeaders($soapHeaders);
        $this->_initSoapClient($soapClient);
    }

    /**
     * Proxy method calls to the SOAPClient instance, transforming method
     * calls and responses for convenience.
     *
     * @param  string  $method  Method name
     * @param  array   $params  Parameters for method
     * @return mixed            Result
     */
    public function __call($method, $params)
    {
        // prepare method name and parameters for soap call
        list($method, $params) = $this->_transformCall($method, $params);
        $params = isset($params[0]) ? array($params[0]) : array();

        // make soap call, capturing the result and output headers
        $outputHeaders = null;
        try {
            $result = $this->_soapClient->__soapCall($method, 
                                                     $params, 
                                                     null, 
                                                     $this->_soapHeaders,
                                                     $outputHeaders);
        } catch (Exception $e) {
            $message = get_class($e) . ': ' . $e->getMessage();
            throw new Zend_Service_StrikeIron_Exception($message, $e->getCode());
        }

        // capture subscription info if returned in output headers
        if (isset($outputHeaders['SubscriptionInfo'])) {
            $info = (object)$outputHeaders['SubscriptionInfo'];
            $this->_subscriptionInfo = new Zend_Service_StrikeIron_ResultDecorator($info);
        }

        // transform/decorate the result and return it                                                 
        $result = $this->_transformResult($result, $method, $params);
        return $result;
    }

    /**
     * Initialize the SOAPClient instance
     *
     * @param  null|object  SOAPClient or equivalent
     * @return void
     */
    protected function _initSoapClient($soapClient)
    {
        if (empty($soapClient)) {
            $soapClient = new SoapClient($this->_wsdl, array('trace' => true, 
                                                             'exceptions' => true));
        }
        $this->_soapClient = $soapClient;        
    }

    /**
     * Initialize the headers to pass to SOAPClient->__soapCall()
     *
     * @param  mixed  Single SoapHeader or array of SoapHeaders
     * @return void
     */
    protected function _initSoapHeaders($soapHeaders)
    {
        // validate headers and check if LicenseInfo was given
        $foundLicenseInfo = false;
        if (isset($soapHeaders)) {
            if (! is_array($soapHeaders)) {
                $soapHeaders = array($soapHeaders);
            }
            
            foreach ($soapHeaders as $header) {
                if (! $header instanceof SoapHeader) {
                    throw new Zend_Service_StrikeIron_Exception('Header must be instance of SoapHeader');
                } else if ($header->name == 'LicenseInfo') {
                    $foundLicenseInfo = true;
                    break;
                }
            }
        } else {
            $soapHeaders = array();
        }
        
        // add default LicenseInfo header if a custom one was not supplied
        if (! $foundLicenseInfo) {
            $soapHeaders[] = new SoapHeader('http://ws.strikeiron.com', 
                                            'LicenseInfo', 
                                            array('RegisteredUser' => array('UserID'   => $this->_username,
                                                                            'Password' => $this->_password)));
        }
        
        $this->_soapHeaders = $soapHeaders;
    }

    /**
     * Transform a method name or method parameters before sending them
     * to the remote service.  This can be useful for inflection or other
     * transforms to give the method call a more PHP-like interface.
     *
     * @see    __call()
     * @param  string  $method  Method name called from PHP
     * @param  mixed   $param   Parameters passed from PHP
     * @return array            [$method, $params] for SOAPClient->__soapCall()
     */
    protected function _transformCall($method, $params)
    {
        return array(ucfirst($method), $params);
    }
    
    /**
     * Transform the result returned from a method before returning
     * it to the PHP caller.  This can be useful for transforming
     * the SOAPClient returned result to be more PHP-like.  
     *
     * The $method name and $params passed to the method are provided to 
     * allow decisions to be made about how to transform the result based
     * on what was originally called.
     *
     * @see    __call()
     * @param  $result  Raw result returned from SOAPClient_>__soapCall()
     * @param  $method  Method name that was passed to SOAPClient->__soapCall()
     * @param  $params  Method parameters that were passed to SOAPClient->__soapCall()
     * @return mixed    Transformed result
     */
    protected function _transformResult($result, $method, $params)
    {
        $resultObjectName = "{$method}Result"; 
        if (isset($result->$resultObjectName)) {
            $result = $result->$resultObjectName;
        }
        if (is_object($result)) {
            $result = new Zend_Service_StrikeIron_ResultDecorator($result);
        }        
        return $result;
    }
    
    /**
     * Get the WSDL URL for this service.
     *
     * @return string
     */
    public function getWsdl()
    {
        return $this->_wsdl;
    }

    /**
     * Get the SOAP Client instance for this service.
     */
    public function getSoapClient()
    {
        return $this->_soapClient;
    }
    
    /**
     * Return the StrikeIron subscription information for this service.
     * If any service method was recently called, the subscription info
     * should have been returned in the SOAP headers so it is cached
     * and returned from the cache.  Otherwise, the getRemainingHits()
     * method is called as a dummy to get the subscription info headers.
     *
     * @param  boolean  $now  Force a call to getRemainingHits instead of cache?
     * @return Zend_Service_StrikeIron_ResultDecorator  Decorated subscription info
     */
    public function getSubscriptionInfo($now = false)
    {
        if ($now || empty($this->_subscriptionInfo)) {
            $this->getRemainingHits();
        }
        return $this->_subscriptionInfo;
    }
}
