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

/** Zend_Loader */
require_once 'Zend/Loader.php';

/** Zend_Service_StrikeIron_Exception */
require_once 'Zend/Service/StrikeIron/Exception.php';

/**
 * This class allows StrikeIron authentication credentials to be specified 
 * in one place and provides a factory for returning instances of different
 * StrikeIron service classes.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_Service_StrikeIron
{
    /**
     * Classes that cannot be used with getService()
     * @param array
     */
    protected $_disallowed = array('ResultDecorator', 'Exception');

    /**
     * Username for StrikeIron services
     * @param string
     */
    protected $_username;
     
    /**
     * Password for StrikeIron services
     * @param string
     */
    protected $_password;
     
    /** 
     * Headers to pass to SOAPClient->__soapCall()
     * @param mixed
     */
    protected $_soapHeaders;
     
    /**
     * SOAPClient instance or equivalent
     * @param SOAPClient|object
     */
    protected $_soapClient;

    
    /**
     * Class constructor
     *
     * @param string  $username     Username for StrikeIron services
     * @param string  $password     Password for StrikeIron services
     * @param mixed   $soapHeaders  Headers to pass to SOAPClient->__soapCall()
     * @param mixed   $soapClient   SOAPClient instance or equivalent
     */
    public function __construct($username, $password, $soapHeaders = null, $soapClient = null)
    {
        $this->_username    = $username;
        $this->_password    = $password;
        $this->_soapHeaders = $soapHeaders;
        $this->_soapClient  = $soapClient;
    }
        
    /**
     * Factory method to return a preconfigured Zend_Service_StrikeIron_*
     * instance.
     *
     * @param  string       $className  Last part of class name, such as "TaxServiceBasic"
     * @param  null|string  $wsdl       URL for custom WSDL, or NULL
     * @return object                   Zend_Service_StrikeIron_* instance
     */
    public function getService($className, $wsdl = null)
    {
        // check class name is actually a strikeiron service
        if (in_array($className, $this->_disallowed)) {
            $msg = "'$className' is not a valid StrikeIron service";
            throw new Zend_Service_StrikeIron_Exception($msg);
        }

        // load the service class
        $fullClass = "Zend_Service_StrikeIron_{$className}";
        try {
            Zend_Loader::loadClass($fullClass);
        } catch (Exception $e) {
            $msg = "Service '$className' could not be loaded: " . $e->getMessage();
            throw new Zend_Service_StrikeIron_Exception($msg, $e->getCode());
        }

        // instantiate and return the service
        $service = new $fullClass($this->_username, 
                                  $this->_password, 
                                  $this->_soapHeaders,
                                  $this->_soapClient,
                                  $wsdl);
        return $service;
    }
    
}
