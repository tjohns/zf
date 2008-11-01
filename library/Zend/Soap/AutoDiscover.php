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
 * @version    $Id$
 */

require_once 'Zend/Server/Interface.php';
require_once 'Zend/Soap/Wsdl.php';
require_once 'Zend/Soap/AutoDiscover/Strategy/Default.php';
require_once 'Zend/Server/Reflection.php';
require_once 'Zend/Server/Exception.php';
require_once 'Zend/Server/Abstract.php';
require_once 'Zend/Uri.php';

/**
 * Zend_Soap_AutoDiscover
 *
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_AutoDiscover implements Zend_Server_Interface {
    /**
     * @var Zend_Soap_Wsdl
     */
    private $_wsdl = null;

    /**
     * @var Zend_Server_Reflection
     */
    private $_reflection = null;

    /**
     * @var array
     */
    private $_functions = array();

    /**
     * @var boolean
     */
    private $_extractComplexTypes;

    /**
     * Discovery Strategy
     */
    protected $_strategy = null;

    /**
     * Url where the WSDL file will be available at.
     *
     * @var WSDL Uri
     */
    protected $_uri;

    /**
     * Constructor
     *
     * @param boolean $extractComplexTypes
     * @param string|Zend_Uri $uri
     */
    public function __construct($extractComplexTypes = true, $uri=null)
    {
        $this->_reflection = new Zend_Server_Reflection();
        $this->_extractComplexTypes = $extractComplexTypes;

        if($uri !== null) {
            $this->setUri($uri);
        }
    }

    /**
     * Set strategy for autodiscovering.
     *
     * @param Zend_Soap_AutoDiscover_Strategy_Interface $strategy
     * @return Zend_Soap_AutoDiscover
     */
    public function setDiscoverStrategy(Zend_Soap_AutoDiscover_Strategy_Interface $strategy)
    {
        $this->_strategy = $strategy;
        return $this;
    }

    /**
     * Return currently set discovery strategy
     * 
     * @return Zend_Soap_AutoDiscover_Strategy_Interface
     */
    public function getDiscoverStrategy()
    {
        if($this->_strategy === null) {
            $this->_strategy = new Zend_Soap_AutoDiscover_Strategy_Default();
        }
        return $this->_strategy;
    }

    /**
     * Set the location at which the WSDL file will be availabe.
     *
     * @see Zend_Soap_Exception
     * @throws Zend_Soap_AutoDiscover_Exception
     * @param  Zend_Uri|string $uri
     * @return Zend_Soap_AutoDiscover
     */
    public function setUri($uri)
    {
        if(is_string($uri)) {
            $uri = Zend_Uri::factory($uri);
        } else if(!($uri instanceof Zend_Uri)) {
            require_once "Zend/Soap/AutoDiscover/Exception.php";
            throw new Zend_Soap_AutoDiscover_Exception("No uri given to Zend_Soap_AutoDiscover::setUri as string or Zend_Uri instance.");
        }
        $this->_uri = $uri;

        // change uri in WSDL file also if existant
        if($this->_wsdl instanceof Zend_Soap_Wsdl) {
            $this->_wsdl->setUri($uri);
        }

        return $this;
    }

    /**
     * Return the current Uri that the SOAP WSDL Service will be located at.
     *
     * @return Zend_Uri
     */
    public function getUri()
    {
        if($this->_uri instanceof Zend_Uri) {
            $uri = $this->_uri;
        } else {
            $schema = "http";
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $schema = 'https';
            }
            $uri = Zend_Uri::factory($schema . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
            $this->setUri($uri);
        }
        return $uri;
    }

    /**
     * Set the Class the SOAP server will use
     *
     * @param string $class Class Name
     * @param string $namespace Class Namspace - Not Used
     * @param array $argv Arguments to instantiate the class - Not Used
     */
    public function setClass($class, $namespace = '', $argv = null)
    {
        $uri = $this->getUri();

        $wsdl = new Zend_Soap_Wsdl($class, $uri, $this->_extractComplexTypes);

        $strategy = $this->getDiscoverStrategy();
        $strategy->setWsdl($wsdl);
        $strategy->setClass($class, $namespace);
        
        $this->_wsdl = $wsdl;
    }

    /**
     * Add a Single or Multiple Functions to the WSDL
     *
     * @param string $function Function Name
     * @param string $namespace Function namespace - Not Used
     */
    public function addFunction($function, $namespace = '')
    {
        if (!is_array($function)) {
            $function = (array) $function;
        }

        $uri = $this->getUri();

        if (!($this->_wsdl instanceof Zend_Soap_Wsdl)) {
            $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
            $name = $parts[0];
            $wsdl = new Zend_Soap_Wsdl($name, $uri, $this->_extractComplexTypes);

            $port = $wsdl->addPortType($name . 'Port');
            $binding = $wsdl->addBinding($name . 'Binding', 'tns:' .$name. 'Port');

            $wsdl->addSoapBinding($binding, 'rpc');
            $wsdl->addService($name . 'Service', $name . 'Port', 'tns:' . $name . 'Binding', $uri);
        } else {
            $wsdl = $this->_wsdl;
        }

        $strategy = $this->getDiscoverStrategy();
        $strategy->setWsdl($wsdl);
        foreach ($function as $func) {
            $this->getDiscoverStrategy()->addFunction($func);
        }
        $this->_wsdl = $wsdl;
    }

    /**
     * Action to take when an error occurs
     *
     * @todo Imeplement
     * @param string $fault
     * @param string|int $code
     */
    public function fault($fault = null, $code = null)
    {

    }

    /**
     * Handle the Request
     *
     * @param string $request A non-standard request - Not Used
     */
    public function handle($request = false)
    {
        if (!headers_sent()) {
            header('Content-Type: text/xml');
        }
        $this->_wsdl->dump();
    }

    /**
     * Return an array of functions in the WSDL
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->_functions;
    }

    /**
     * Load Functions
     *
     * @todo Implement
     * @param unknown_type $definition
     */
    public function loadFunctions($definition)
    {

    }

    /**
     * Set Persistance
     *
     * @todo Implement
     * @param int $mode
     */
    public function setPersistence($mode)
    {

    }

    /**
     * Returns an XSD Type for the given PHP type
     *
     * @param string $type PHP Type to get the XSD type for
     * @return string
     */
    public function getType($type)
    {
        if (!($this->_wsdl instanceof Zend_Soap_Wsdl)) {
            /** @todo Exception throwing may be more correct */

            // WSDL is not defined yet, so we can't recognize type in context of current service
            return '';
        } else {
            return $this->_wsdl->getType($type);
        }
    }
}

