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
 * @package    Zend_XmlRpc
 * @subpackage Client
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * For handling the HTTP connection to the XML-RPC service
 */
require_once 'Zend/Http/Client.php';

/**
 * Exception this class throws
 */
require_once 'Zend/XmlRpc/Client/Exception.php';

/**
 * Enables object chaining for calling namespaced XML-RPC methods.
 */
require_once 'Zend/XmlRpc/Client/NamespaceDecorator.php';

/**
 * Represent a native XML-RPC value, used both in sending parameters
 * to methods and as the parameters retrieve from method calls
 */
require_once 'Zend/XmlRpc/Value.php';


/**
 * An XML-RPC client implementation
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Client
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_XmlRpc_Client
{
    /**
     * Different types for the response: as PHP native types, original XML or in Zend_XmlRpc_Value object
     */
    const RESPONSE_PHP_NATIVE     = 'php_native';
    const RESPONSE_XML_STRING     = 'xml_string';
    const RESPONSE_ZXMLRPC_OBJECT = 'Zend_XmlRpc_Value_object';

    /**
     * The XML-RPC service server full address
     *
     * @var string
     */
    protected $_serverAddress;

    /**
     * The HTTP client object to use for connecting the XML-RPC server.
     *
     * @var Zend_Http_client_Abstract
     */
    protected $_httpClient = null;

    /**
     * The response from an XML-RPC method call, held in a Zend_XmlRpc_Value object
     *
     * @var Zend_XmlRpc_Value|null
     */
    protected $_response = null;

    /**
     * Array of cached namespace decorators, array of Zend_XmlRpc_Client_NamespaceDecorator objects
     *
     * @var array
     */
    protected $_namespaceDecorators = array();


    /**
     * Holding all the method signatures, the array has the methods name as keys and the signature as the value
     * The signature is an array of 2 keys:
     *      'return_value' - string, hold the return value of the method
     *      'params'       - array of strings, hold the parameters for the method (can be an empty array)
     *
     * This array is created automatically when calling the __getMethodsXml() or __setMethodsXml() methods
     *
     * @var array
     */
    protected $_methodSignatures = array();


    /**
     * Class constructor - create a new XML-RPC client to a remote server
     *
     * @param string $server      Full address of the XML-RPC service
     *                            (e.g. http://time.xmlrpc.com/RPC2)
     *
     * @param string $methodsXml  Method signatures in XML format, used for type
     *                            hinting during the PHP->XMLRPC type conversions.
     *                            {@see __getMethodsXml() for more information}
     */
    public function __construct($server, $methodsXml = null)
    {
        $this->_serverAddress = $server;
        $this->__setMethodsXml($methodsXml);
    }


    /**
     * Undefined properties are assumed to be XML-RPC namespaces
     * and return a decorator to enable object chains.
     *
     * @param  string $namespace
     * @return Zend_XmlRpc_Client_NamespaceDecorator
     */
    public function __get($namespace)
    {
        if (!isset($this->_namespaceDecorators[$namespace])) {
            $this->_namespaceDecorators[$namespace] = new Zend_XmlRpc_Client_NamespaceDecorator($namespace, $this);
        }

        return $this->_namespaceDecorators[$namespace];
    }


     /**
     * Using the magic __call function to call methods directly by method name
     *
     * @param string $methodName  The method we call from the service
     * @param array  $params      Array of parameters for the method call
     */
    public function __call($methodName, $params)
    {
        // Convert the parameters to Zend_XmlRpc_Value objects
        $this->_convertParams($params, $methodName);

        return $this->_sendRequest($methodName, $params);
    }


    /**
     * Call a specific method (with or without parameters) from the XML-RPC service
     *
     * @param string $methodName  The method we call from the service
     * @param varargs             Optional, Parameters to pass the method, multiple
     *                            (using func_get_args() function) parameter of native
     *                            PHP types or Zend_XmlRpc_Value objects
     */
    public function __xmlrpcCall($methodName)
    {
        $params = func_num_args() > 1 ? array_shift(func_get_args())
                                                       : null;

        return $this->__call($methodName, $params);
    }


    /**
     * Generates an XML string containing the signatures for every method
     * on the remote server by calling system.listMethods() and then
     * calling system.methodSignature() for each method.  This output
     * generated is analogous to SOAP's WSDL file.
     *
     * @return string XML representing the signatures of all server methods, analogous to SOAP's WSDL file
     */
    public function __getMethodsXml()
    {
        try {
            // Get a list of all methods on the server.
            $methodNames = $this->{'system.listMethods'}();
        } catch (Zend_XmlRpc_Client_Exception $e) {
            // If the exception that was caught is Zend_XmlRpc_Client_Exception, it means that (most probably)
            // the system.listMethods method is not supported, we throw an exception
            throw new Zend_XmlRpc_Client_Exception('Cannot get the server methods: '. $e->getMessage());
        }

        // The system.methodSignature() method doesn't exists on on the server
        if (array_search('system.methodSignature', $methodNames) === false) {
            throw new Zend_XmlRpc_Client_Exception('Cannot get the server methods signature since the server does not support the \'system.methodSignature\' method');
        }

        // Get the signatures of all the methods on the server
        if (array_search('system.multicall', $methodNames) !== false) {
            // The system.multicall method exists in the server, we use it to get all the signatures in one request
            $signatures = $this->_getSignaturesMultiCall($methodNames);
        } else {
            // The system.multicall method does not exists in the server,
            // we get the signature of each method one by one
            $signatures = $this->_getSignatures($methodNames);
        }

        return $this->_buildMethodsXML($signatures);
    }


    /**
     * Consumes an XML string generated by __getMethodsXml() and uses it
     * for type hinting.  When a remote method is called, either trapped by
     * __call() or called with __xmlRpcCall(),
     *
     * @param null|string $methodsXml Use null for clearing the current method signatures
     */
    public function __setMethodsXml($methodsXml = null)
    {
        if ($methodsXml === null) {
            $this->_methodSignatures = array();
            return;
        }

        // Using simple XML to parse the "WSDL" file of signatures
        try {
            $simple_xml = @new SimpleXMLElement($methodsXml);
        } catch (Exception $e) {
            // The methods XML is not a valid XML string
            throw new Zend_XmlRpc_Client_Exception('Failed to parse methods signatures: '. $e->getMessage(),$e->getCode());
        }

        // Parse xml method signatures into associative array for type hinting
        $this->_methodSignatures = array();

        if (empty($simple_xml->method)) {
            // The XML must contain method tags
            throw new Zend_XmlRpc_Client_Exception('Failed to parse methods signatures, cannot find METHOD tags');
        }

        foreach ($simple_xml->method as $methodXml) {
            if (empty($methodXml->name)) {
                // The method tag must contain a name tag
                throw new Zend_XmlRpc_Client_Exception('Failed to parse methods signatures, METHOD tag must contain a NAME tag');
            }
            if (empty($methodXml->signature)) {
                // The method tag must contain a signature tag
                throw new Zend_XmlRpc_Client_Exception('Failed to parse methods signatures, METHOD tag must contain a SIGNATURE tag');
            }
            if (empty($methodXml->signature->returnValue)) {
                // The method tag must contain a returnValue tag
                throw new Zend_XmlRpc_Client_Exception('Failed to parse methods signatures, SIGNATURE tag must contain a returnValue tag');
            }
            if (empty($methodXml->signature->params)) {
                // The method tag must contain a params tag
                throw new Zend_XmlRpc_Client_Exception('Failed to parse methods signatures, SIGNATURE tag must contain a PARAMS tag');
            }

            // Parse the method parameters
            $methodParams = array();
            foreach ($methodXml->signature->params->param as $param) {
                $methodParams[] = (string)$param;
            }

        	$this->_methodSignatures[(string)$methodXml->name] = array(
        	   'return_value' => (string)$methodXml->signature->returnValue,
        	   'params'       => $methodParams
        	);
        }
    }


    /**
     * The response received from the method call, response can be retrieved in 3 formats:
     *  - as a PHP varaible,
     *  - as XML string,
     *  - or in the Zend_XmpRpc_Value object
     *
     * @param Zend_XmlRpc_Client::RESPONSE_* $type The response value types, options are:
     *                                             PHP native type, original XML string or Zend_XmlRpc_Value object
     * @return mixed The response of the service
     */
    public function __getResponse($type = self::RESPONSE_PHP_NATIVE)
    {
        if (!$this->_response instanceof Zend_XmlRpc_Value) {
            throw new Zend_XmlRpc_Client_Exception('Response was not received yet');
        }

        switch ($type) {
            case self::RESPONSE_PHP_NATIVE:
                return $this->_response->getValue();

            case self::RESPONSE_XML_STRING:
                return $this->_response->getAsXML();

            case self::RESPONSE_ZXMLRPC_OBJECT:
                return $this->_response;

            default:
                throw new Zend_XmlRpc_Client_Exception('Invalid type requested for the response');
        }
    }


    /**
     * Sets the HTTP client object to use for connecting the XML-RPC server.
     * If none is set, the a default Zend_Http_Client will be used.
     *
     * @param Zend_Http_Client_Abstract $httpClient
     */
    public function __setHttpClient(Zend_Http_Client_Abstract $httpClient)
    {
        $this->_httpClient = $httpClient;
    }


    /**
	 * Gets the HTTP client object.
	 *
	 * @return Zend_Http_Client_Abstract
	 */
	protected function __getHttpClient()
	{
		if (!$this->_httpClient instanceof Zend_Http_Client_Abstract) {
			$this->_httpClient = new Zend_Http_Client();
		}

		return $this->_httpClient;
	}


    /**
     * Send a XML-RPC request to the service (for a specific method)
     *
     * @param string $method Name of the method we want to call
     * @param array $params Array of Zend_XmlRpc_Value objects, parameters for the method
     * @throws Zend_Http_Client_Exception
     */
    protected function _sendRequest($method, $params=null)
    {
        $request_data = $this->_buildRequest($method, $params);

        $http = $this->__getHttpClient();

        // Set the server address
        $http->setUri($this->_serverAddress);
        // Set the content-type header as text/xml
        // What if the given HTTP client already has headres ? it shouldn't override them
        $http->setHeaders(array('Content-Type: text/xml'));

        $response = $http->post($request_data);
        /* @var $response Zend_Http_Response */

        return $this->_parseResponse($response->getBody());
    }


    /**
     * Build the XML body of an XML-RPC request
     *
     * @param string $method Name of the method we want to call
     * @param array $params Array of Zend_XmlRpc_Value objects, parameters for the method
     * @return string The XML body
     */
    protected function _buildRequest($method, $params=null)
    {
        $data = '<?xml version="1.0"?>' ."\n"
              . '<methodCall>'          ."\n"
              . '<methodName>'
              .     $method
              . '</methodName>'         ."\n";

        if (is_array($params)) {
            $data .= '<params>'         ."\n";

            foreach ($params as $param) {
                /* @var $param Zend_XmlRpc_Value */

                $data .= '<param>'          ."\n"
                       .    $param->getAsXML()
                       . '</param>'         ."\n";
            }

            $data .= '</params>'            ."\n";
        }

        return $data . '</methodCall>'      ."\n";
    }


    /**
     * Parse the response from a XML-RPC method call, hold the response as a Zend_XmlRpc_Value object
     * The response parameter must be a valid XML string
     *
     * @param string $response XML string to parse
     */
    protected function _parseResponse($response)
    {
        if (!$response) {
            // The response is empty
            throw new Zend_XmlRpc_Client_Exception('Received empty response');
        }
        // Using simple XML to parse the response
        try {
            $simple_xml = @new SimpleXMLElement($response);
        } catch (Exception $e) {
            // The response is not a valid XML
            throw new Zend_XmlRpc_Client_Exception('Failed to parse response: '. $e->getMessage(),$e->getCode());
        }

        // We check if this is a Fault response
        if (!empty($simple_xml->fault)) {
            // The fault tag exists and not empty, the request failed and returned a fault response
            if (empty($simple_xml->fault->value)) {
                // Invalid fault response, fault tag must contain a value tag
                throw new Zend_XmlRpc_Client_Exception('Invalid fault response, FAULT tag must contain a VALUE tag');
            }

            // Parse the fault response into a Zend_XmlRpc_Value object
            try {
                $this->_response = Zend_XmlRpc_Value::getXmlRpcValue($simple_xml->fault->value, Zend_XmlRpc_Value::XML_STRING);
            } catch (Zend_XmlRpc_Value_Exception $e) {
                // Failed to create a Zend_XmlRpc_Value object from the fault
                throw new Zend_XmlRpc_Client_Exception('Invalid fault response, '. $e->getMessage());
            }
            $fault = $this->_response->getValue();
            throw new Zend_XmlRpc_Client_Exception('Request failed, '. $fault['faultString'], $fault['faultCode']);
        } elseif (empty($simple_xml->params)) {
            // Invalid response, no params tag
            throw new Zend_XmlRpc_Client_Exception('Invalid response, method response must contain a PARAMS tag');
        } elseif (empty($simple_xml->params->param)) {
            // Invalid response, params tag must contain a param tag
            throw new Zend_XmlRpc_Client_Exception('Invalid response, PARAMS tag must contain a PARAM tag');
        } elseif (empty($simple_xml->params->param->value)) {
            // Invalid response, param tag must contain a value tag
            throw new Zend_XmlRpc_Client_Exception('Invalid response, PARAM tag must contain a VALUE tag');
        }

        // Parse the success response into a Zend_XmlRpc_Value object
        $this->_response = Zend_XmlRpc_Value::getXmlRpcValue($simple_xml->params->param->value, Zend_XmlRpc_Value::XML_STRING);

        return $this->__getResponse(self::RESPONSE_PHP_NATIVE);
    }


    /**
     * Use system.methodSignature() to get all the given methods signatures using the system.multicall() method
     *
     * Attempt to get the method signatures in one request via system.multicall().
     * This is a boxcar feature of XML-RPC and is found on fewer servers, however
     * can significantly improve performance if present.  For more information on
     * system.multicall(), see: http://www.xmlrpc.com/discuss/msgReader$1208
     *
     * @param array $methodNames Array of method names to get their signatures
     * @return array Structure of array is method_name => method signature as returned from system.methodSignature() call
     */
    private function _getSignaturesMultiCall($methodNames)
    {
        $multicallParams = array();
        foreach ($methodNames as $method) {
            $multicallParams[] = array('methodName' => 'system.methodSignature',
                                       'params'     => array($method));
        }

        $tmpSignatures = $this->{'system.multicall'}($multicallParams);

        if (count($tmpSignatures) != count($methodNames)) {
            // For some reason, the number of signatures doesn't match the number of methods, arrays cannot be combined
            throw new Zend_XmlRpc_Client_Exception('Remote server does not appear to support the necessary introspection capabilities');
        }

        // Create a new signatures array with the methods name as keys and the signature as value
        $signatures = array();
        foreach ($tmpSignatures as $i => $signature) {
            if (!is_array($signature)) {    // This signature is invalid
                continue;
            }
            $signatures[$methodNames[$i]] = $signature[0];
        }

        return $signatures;
    }


    /**
     * Call system.methodSignature() for all the given methods
     *
     * @param array $methodNames Array of method names to get their signatures
     * @return array Structure of array is method_name => method signature as returned from system.methodSignature() call
     */
    private function _getSignatures($methodNames)
    {
        $signatures = array();

        foreach ($methodNames as $method) {
            $signatures[$method] = $this->{'system.methodSignature'}($method);
        }

        return $signatures;
    }


    /**
     * Generates an XML string analogous to SOAP's WSDL representing all the methods signatures
     * Signature is the method return value and method parameters
     * This function also set the _methodSignatures data memeber with the method signatures details (in an array)
     *
     * @param array $signatures Array in the structure of method name => array (return value, parameters)
     * @return string XML representing the signatures of all server methods, analogous to SOAP's WSDL file
     */
    protected function _buildMethodsXML($signatures)
    {
        // Reset the method signatures array
        $this->_methodSignatures = array();

        $xml = "<?xml version='1.0' standalone='yes'?>\n"
             . "<methods>\n";

        foreach ($signatures as $methodName => $signature) {
            if (!is_array($signature[0])) {
                // The signature of this method is invalid (should be an array), we ignore this method
                continue;
            }

            $xml .= "\t<method>\n"
                  . "\t\t<name>$methodName</name>\n"
                  . "\t\t<signature>\n";

            // After popping the return value, the $signature[0] array is holding the method parameters
            $returnValue = array_shift($signature[0]);

            $xml .= "\t\t\t<returnValue>$returnValue</returnValue>\n"
                  . "\t\t\t<params>\n";

            foreach ($signature[0] as $param) {
                $xml .= "\t\t\t\t<param>$param</param>\n";
            }

            $xml .= "\t\t\t</params>\n"
                  . "\t\t</signature>\n"
                  . "\t</method>\n";

            $this->_methodSignatures[$methodName] = array('return_value' => $returnValue,
                                                          'params'       => $signature[0]);
        }

        return $xml . "</methods>";
    }


    /**
     * Convert an array of PHP variables into XML-RPC native types represented by Zend_XmlRpc_Value objects
     * If method name is given, try to use the _methodSignatures data member for type hinting,
     * if not, auto convert the PHP variable types into XML-RPC types
     *
     * @param array $params Array of PHP varaibles and/or Zend_XmlRpc_Value objects
     *                      The parameter is passed by reference
     * @param string $methodName If set, the type hinting will be according to the parameters of this method
     * @return array|null Array of Zend_XmlRpc_Value objects
     */
    protected function _convertParams(&$params, $methodName = null)
    {
        if (!is_array($params)) {
            $params = null;
            return;
        }

        $paramsTypeHinting = $this->_getMethodParams($methodName);
        /** @todo what we do if count($paramsTypeHinting) differ than count($params) ? maybe nothing */

        foreach ($params as $i => &$param) {
            if (!$param instanceof Zend_XmlRpc_Value) {
                // Need to convert the parameter to Zend_XmlRpc_Value object
                // If there is type hinting for this method, we use it, otherwise
                // the convertion is done according to the PHP type
                if (isset($paramsTypeHinting[$i])) {
                    $param = Zend_XmlRpc_Value::getXmlRpcValue($param, $paramsTypeHinting[$i]);
                } else {
                    $param = Zend_XmlRpc_Value::getXmlRpcValue($param);
                }
            }
        }
    }


    /**
     * Return the XML-RPC types of the necessary parameters for a method in the service
     * Get the types from the _methodSignatures data member
     * {@see __getMethodsXml() for more information}
     *
     * @param string $methodName
     * @return array XML-RPC types of the method parameters (or an empty array if there are no parameters)
     */
    protected function _getMethodParams($methodName)
    {
        if (isset($this->_methodSignatures[$methodName])) {
            return $this->_methodSignatures[$methodName]['params'];
        }
        return array();
    }


}

