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
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Soap_Client_Exception */
require_once 'Zend/Soap/Client/Exception.php';

/** Zend_Soap_Server */
require_once 'Zend/Soap/Server.php';

/** Zend_Soap_Client_Local */
require_once 'Zend/Soap/Client/Local.php';


/**
 * Zend_Soap_Client
 *
 * @category   Zend
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Soap_Client
{
    /**
     * Encoding
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Array of SOAP type => PHP class pairings for handling return/incoming values
     * @var array
     */
    protected $_classmap = null;

    /**
     * Registered fault exceptions
     * @var array
     */
    protected $_faultExceptions = array();

    /**
     * SOAP version to use; SOAP_1_2 by default, to allow processing of headers
     * @var int
     */
    protected $_soapVersion = SOAP_1_2;

    /** Set of other SoapClient options */
    protected $_uri                 = null;
    protected $_location            = null;
    protected $_style               = null;
    protected $_use                 = null;
    protected $_login               = null;
    protected $_password            = null;
    protected $_proxy_host          = null;
    protected $_proxy_port          = null;
    protected $_proxy_login         = null;
    protected $_proxy_password      = null;
    protected $_local_cert          = null;
    protected $_passphrase          = null;
    protected $_compression         = null;
    protected $_connection_timeout  = null;

    /**
     * WSDL used to access server
     * It also defines Zend_Soap_Client working mode (WSDL vs non-WSDL)
     *
     * @var string
     */
    protected $_wsdl = null;

	/**
	 * SoapClient object
	 *
	 * @var SoapClient
	 */
	protected $_soapClient;

	/**
	 * Zend_Soap_Server object to process requests locally
	 *
	 * @var Zend_Soap_Server
	 */
    protected $_localServer = null;


    /**
     * Constructor
     *
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($wsdl = null, $options = null)
    {
        if ($wsdl !== null) {
            $this->setWsdl($wsdl);
        }
        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * Set wsdl
     *
     * @param string $wsdl
     * @return Zend_Soap_Client
     */
    public function setWsdl($wsdl)
    {
    	$this->_wsdl = $wsdl;
        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get wsdl
     *
     * @return string
     */
    public function getWsdl()
    {
        return $this->_wsdl;
    }

    /**
     * Set Options
     *
     * Allows setting options as an associative array of option => value pairs.
     *
     * @param  array $options
     * @return Zend_Soap_Client
     * @throws Zend_SoapClient_Exception
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'classmap':
                case 'classMap':
                    $this->setClassmap($value);
                    break;
                case 'encoding':
                    $this->setEncoding($value);
                    break;
                case 'soapVersion':
                case 'soap_version':
                    $this->setSoapVersion($value);
                    break;
                case 'wsdl':
                    $this->setWsdl($value);
                    break;
                case 'uri':
                    $this->_uri = $value;
                    break;
                case 'location':
                    $this->_location = $value;
                    break;
                case 'style':
                    $this->_style = $value;
                    break;
                case 'use':
                    $this->_use = $value;
                    break;
                case 'login':
                    $this->_login = $value;
                    break;
                case 'password':
                    $this->_password = $value;
                    break;
                case 'proxy_host':
                    $this->_proxy_host = $value;
                    break;
                case 'proxy_port':
                    $this->_proxy_port = $value;
                    break;
                case 'proxy_login':
                    $this->_proxy_login = $value;
                    break;
                case 'proxy_password':
                    $this->_proxy_password = $value;
                    break;
                case 'local_cert':
                    $this->_local_cert = $value;
                    break;
                case 'passphrase':
                    $this->_passphrase = $value;
                    break;
                case 'compression':
                    $this->_compression = $value;
                    break;
                case 'connection_timeout':
                    $this->_connection_timeout = $value;
                    break;

                default:
                    throw new Zend_Soap_Client_Exception('Unknown SOAP client option');
                    break;
            }
        }

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Return array of options suitable for using with SoapClient constructor
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array();
        if (null !== $this->_classmap) {
            $options['classmap'] = $this->getClassmap();
        }
        if (null !== $this->_encoding) {
            $options['encoding'] = $this->getEncoding();
        }
        if (null !== $this->_soapVersion) {
            $options['soap_version'] = $this->getSoapVersion();
        }
        if (null !== $this->_wsdl) {
            $options['wsdl'] = $this->_wsdl;
        }
        if (null !== $this->_uri) {
            $options['uri'] = $this->_uri;
        }
        if (null !== $this->_location) {
            $options['location'] = $this->_location;
        }
        if (null !== $this->_style) {
            $options['style'] = $this->_style;
        }
        if (null !== $this->_use) {
            $options['use'] = $this->_use;
        }
        if (null !== $this->_login) {
            $options['login'] = $this->_login;
        }
        if (null !== $this->_password) {
            $options['password'] = $this->_password;
        }
        if (null !== $this->_proxy_host) {
            $options['proxy_host'] = $this->_proxy_host;
        }
        if (null !== $this->_proxy_port) {
            $options['proxy_port'] = $this->_proxy_port;
        }
        if (null !== $this->_proxy_login) {
            $options['proxy_login'] = $this->_proxy_login;
        }
        if (null !== $this->_proxy_password) {
            $options['proxy_password'] = $this->_proxy_password;
        }
        if (null !== $this->_local_cert) {
            $options['local_cert'] = $this->_local_cert;
        }
        if (null !== $this->_passphrase) {
            $options['passphrase'] = $this->_passphrase;
        }
        if (null !== $this->_compression) {
            $options['compression'] = $this->_compression;
        }
        if (null !== $this->_connection_timeout) {
            $options['connection_timeout'] = $this->_connection_timeout;
        }

        return $options;
    }

    /**
     * Set SOAP version
     *
     * @param  int $version One of the SOAP_1_1 or SOAP_1_2 constants
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid soap version argument
     */
    public function setSoapVersion($version)
    {
        if (!in_array($version, array(SOAP_1_1, SOAP_1_2))) {
            throw new Zend_Soap_Client_Exception('Invalid soap version specified. Use SOAP_1_1 or SOAP_1_2 constants.');
        }

        $this->_soapVersion = $version;
        return $this;
    }

    /**
     * Get SOAP version
     *
     * @return int
     */
    public function getSoapVersion()
    {
        return $this->_soapVersion;
    }

    /**
     * Set classmap
     *
     * @param  array $classmap
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception for any invalid class in the class map
     */
    public function setClassmap(array $classmap)
    {
        foreach ($classmap as $type => $class) {
            if (!class_exists($class)) {
                throw new Zend_Soap_Class_Exception('Invalid class in class map');
            }
        }

        $this->_classmap = $classmap;
        return $this;
    }

    /**
     * Retrieve classmap
     *
     * @return mixed
     */
    public function getClassmap()
    {
        return $this->_classmap;
    }

    /**
     * Set encoding
     *
     * @param  string $encoding
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid encoding argument
     */
    public function setEncoding($encoding)
    {
        if (!is_string($encoding)) {
            throw new Zend_Soap_Client_Exception('Invalid encoding specified');
        }

        $this->_encoding = $encoding;
        return $this;
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Retrieve request XML
     *
     * @return string
     */
    public function getLastRequest()
    {
    	if ($this->_soapClient !== null) {
    		return $this->_soapClient->__getLastRequest();
    	}

        return '';
    }

    /**
     * Get response XML
     *
     * @return string
     */
    public function getLastResponse()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastResponse();
        }

        return '';
    }

    /**
     * Set Zend_Soap_Server object to process requests locally
     *
     * @param Zend_Soap_Server $server
     * @return Zend_Soap_Client
     */
    public function setLocalServer(Zend_Soap_Server $server)
    {
    	$this->_localServer = $server;
    }

    /**
     * Initialize SOAP Client object
     *
     * @throws Zend_Search_Client_Exception
     */
    protected function _initSoapClientObject()
    {
        $wsdl = $this->getWsdl();
        $options = array_merge($this->getOptions(), array('trace' => true));


        if ($wsdl == null) {
            if (!isset($options['location'])) {
                throw new Zend_Search_Client_Exception('\'location\' parameter is required in non-WSDL mode.');
            }
            if (!isset($options['uri'])) {
                throw new Zend_Search_Client_Exception('\'uri\' parameter is required in non-WSDL mode.');
            }
        } else {
            if (isset($options['use'])) {
                throw new Zend_Search_Client_Exception('\'use\' parameter only works in non-WSDL mode.');
            }
            if (isset($options['style'])) {
                throw new Zend_Search_Client_Exception('\'style\' parameter only works in non-WSDL mode.');
            }
        }
        unset($options['wsdl']);

        if ($this->_localServer === null) {
        	$this->_soapClient = new SoapClient($wsdl, $options);
        } else {
        	$this->_soapClient = new Zend_Soap_Client_Local($this->_localServer, $wsdl, $options);
        }
    }

    /**
     * Perform a SOAP call
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
    	if ($this->_soapClient == null) {
    		$this->_initSoapClientObject();
        }

        return call_user_func_array(array($this->_soapClient, $name), $arguments);
    }

    /**
     * Return a list of available functions
     *
     * @return array
     * @throws Zend_Search_Client_Exception
     */
    public function getFunctions()
    {
    	if ($this->getWsdl() == null) {
    		throw new Zend_Search_Client_Exception('\'getFunctions\' method is available only in WSDL mode.');
    	}

        if ($this->_soapClient == null) {
            $this->_initSoapClientObject();
        }

        return $this->_soapClient->__getFunctions();
    }
}
