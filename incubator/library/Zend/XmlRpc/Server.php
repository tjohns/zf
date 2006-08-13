<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_XmlRpc
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/**
 * Zend
 */
require_once 'Zend.php';

/**
 * Exception this class throws
 */
require_once 'Zend/XmlRpc/Server/Exception.php';

/**
 * XMLRPC server fault class
 */
require_once 'Zend/XmlRpc/Server/Fault.php';

/**
 * Parse callbacks for method help and signatures
 */
require_once 'Zend/XmlRpc/Server/CallbackParser/Core.php';

/**
 * Convert PHP to and from xmlrpc native types
 */
require_once 'Zend/XmlRpc/Value.php';

/**
 * An XML-RPC server implementation
 *
 * Example:
 * <code>
 * require_once 'Zend/XmlRpc/Server.php';
 * require_once 'Zend/XmlRpc/Server/Cache.php';
 * require_once 'Zend/XmlRpc/Server/Fault.php';
 * require_once 'My/Exception.php';
 * require_once 'My/Fault/Observer.php';
 *
 * // Instantiate server
 * $server = new Zend_XmlRpc_Server();
 *
 * // Allow some exceptions to report as fault responses:
 * Zend_XmlRpc_Server_Fault::attachFaultException('My_Exception');
 * Zend_XmlRpc_Server_Fault::attachObserver('My_Fault_Observer');
 *
 * // Get or build dispatch table:
 * if (!Zend_XmlRpc_Server_Cache::get($filename, $server)) {
 *     require_once 'Some/Service/Class.php';
 *     require_once 'Another/Service/Class.php';
 *
 *     // Attach Some_Service_Class in 'some' namespace
 *     $server->setClass('Some_Service_Class', 'some');
 *
 *     // Attach Another_Service_Class in 'another' namespace; use only static 
 *     // methods
 *     $server->setClass('Another_Service_Class', 'another', false);
 *
 *     // Create dispatch table cache file
 *     Zend_XmlRpc_Server_Cache::save($filename, $server);
 * }
 *
 * $response = $server->handle();
 * echo $response;
 * </code>
 *
 * @package    Zend_XmlRpc
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_XmlRpc_Server
{
    /**
     * Argument(s) passed in request
     * @var mixed
     */
    protected $_args;

    /**
     * Array of dispatchable methods
     * @var array
     */
    protected $_methods = array();

    /**
     * Name of requested method
     * @var string
     */
    protected $_method;

    /**
     * Last response value
     * @var mixed 
     */
    protected $_response;

    /**
     * response XML
     * @var string
     */
    protected $_responseXml;

   /**
     * XML from request
     * @var string
     */
    protected $_requestXml;

    /**
     * System.* methods
     * @var array
     */
    public static $systemMethods = array(
        'system.listMethods',
        'system.methodHelp',
        'system.methodSignature',
        'system.multicall'
    );

    /**
     * Constructor
     *
     * Sets encoding to UTF-8 and creates system.* methods.
     *
     * @return void
     */
    public function __construct()
    {
        // Set internal encoding to UTF-8
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $system = array(
            'listMethods',
            'methodHelp',
            'methodSignature',
            'multicall'
        );
        foreach ($system as $method) {
            $reflection = new ReflectionMethod($this, $method);
            $dispatch = Zend_XmlRpc_Server_CallbackParser_Core::getDispatchFromComment($reflection->getDocComment());
            $xmlRpcMethod = 'system.' . $method;
            $dispatch['callback'] = array($this, $method);
            $this->_methods[$xmlRpcMethod] = $dispatch;
        }
    }

    /**
     * Attach a function as an XMLRPC method
     *
     * Attaches a function as an XMLRPC method, prefixing the XMLRPC method with 
     * $namespace, if provided. Reflection is done on the function's docblock to 
     * create the methodHelp for the XMLRPC method.
     *
     * @param array $function
     * @param string $namespace
     * @return void
     * @throws Zend_XmlRpc_Server_Exception
     */
    public function addFunction($function, $namespace = '') 
    {
        if (!is_string($function) || !function_exists($function)) {
            throw new Zend_XmlRpc_Server_Exception('Unable to attach function; invalid function name', 611);
        }

        Zend::loadClass('Zend_XmlRpc_Server_CallbackParser_Function');
        $parser  = new Zend_XmlRpc_Server_CallbackParser_Function();
        $methods = $parser->parse($function, $namespace);
        $this->_methods = array_merge_recursive($this->_methods, $methods);
    }

    /**
     * Load methods from an array
     *
     * Method definitions are stored internally and loaded from arrays using the 
     * following structure:
     * <code>
     * $methods = array(
     *     'methodName' => array(
     *         'function|method|static' => $string|array(
     *             'class'  => $string,
     *             'method' => $string
     *         ),
     *         'params' => $array, // Optional; constructor params for method 
     *                             // callbacks
     *         'methodHelp' => $string,
     *         'signatures' => array(
     *             array(
     *                 $returnValType,
     *                 $paramType[,
     *                 $paramType[,
     *                 ...]]
     *             )[, ...]
     *         )
     *     )
     * );
     * </code>
     *
     * Each method definition must have either a 'function', 'method', or 'static' key, as follows:
     * - 'function' indicates that the callback is a function
     * - 'static' indicates that the callback is a class static method
     * - 'method' indicates that the callback is an object instance method, and 
     *   that the class associated with it will need to be instantiated prior to 
     *   the call.
     *
     * If the method type is 'method', the method definition may have an 
     * optional 'params' array, which contains an array of arguments to pass to 
     * the class constructor.
     *
     * The 'signatures' entry holds an array of method signatures. each 
     * signature has at least one entry, the return value; each entry following 
     * the first is an argument that should be passed to the method, and is an 
     * XMLRPC type.
     *
     * If $namespace is passed, this value will be used to prefix all methods.
     *
     * Typically, you will not use this method; it will be called using the 
     * results pulled from {@link Zend_XmlRpc_Server_Cache::get()}.
     * 
     * @param mixed $array 
     * @param string $namespace Optional
     * @return void
     * @throws Zend_XmlRpc_Server_Exception on invalid input
     */
    public function loadArray($array, $namespace = '')
    {
        if (!is_array($array)) {
            throw new Zend_XmlRpc_Server_Exception('Unable to load array; not an array', 612);
        }

        Zend::loadClass('Zend_XmlRpc_Server_CallbackParser_Array');
        $parser  = new Zend_XmlRpc_Server_CallbackParser_Array();
        $methods = $parser->parse($array, $namespace);
        $this->_methods = array_merge_recursive($this->_methods, $methods);
    }

    /**
     * Attach class methods as XMLRPC method handlers
     *
     * $class may be either a class name or an object. Reflection is done on the 
     * class or object to determine the available public methods, and each is 
     * attached to the server as an available method; if a $namespace has been 
     * provided, that namespace is used to prefix the XMLRPC method names.
     *
     * If $instantiate is set to false, the server will only attach public 
     * static methods for the class.
     * 
     * @param string|object $class 
     * @param string $namespace Optional
     * @param boolean $instantiate Optional; defaults to true
     * @return void
     * @throws Zend_XmlRpc_Server_Exception on invalid input
     */
    public function setClass($class, $namespace = '', $instantiate = true)
    {
        if (is_string($class) && !class_exists($class)) {
            if (!class_exists($class)) {
                throw new Zend_XmlRpc_Server_Exception('Invalid method class', 610);
            }
        }

        $argv = false;
        if (3 < func_num_args()) {
            $argv = func_get_args();
            $argv = array_slice($argv, 3);
        }

        $parserClass = 'Zend_XmlRpc_Server_CallbackParser_Object';
        if (!$instantiate && is_string($class)) {
            $parserClass = 'Zend_XmlRpc_Server_CallbackParser_Class';
        }

        Zend::loadClass($parserClass);
        $parser = new $parserClass();
        $methods = $parser->parse($class, $namespace, $argv);
        $this->_methods = array_merge_recursive($this->_methods, $methods);
    }

    /**
     * Raise an xmlrpc server fault
     * 
     * @param string|Exception $fault 
     * @param int $code 
     * @return Zend_XmlRpc_Server_Fault
     */
    public function fault($fault, $code = 404)
    {
        if (!$fault instanceof Exception) {
            $fault = (string) $fault;
            $fault = new Zend_XmlRpc_Server_Exception ($fault, $code);
        }

        return Zend_XmlRpc_Server_Fault::getInstance($fault);
    }

    /**
     * Handle an xmlrpc call (actual work)
     *
     * @param string $method
     * @param array $args
     * @throws Zend_XmlRpc_Server_Exception if invalid method
     */
    protected function _handle($method, $args) 
    {
        // Check for valid method
        if (!isset($this->_methods[$method])) {
            throw new Zend_XmlRpc_Server_Exception('Method does not exist', 404);
        }

        $info = $this->_methods[$method];
        switch (true) {
            case (isset($info['function'])):
                $callback = $info['function'];
                break;
            case (isset($info['callback'])):
                $callback = $info['callback'];
                break;
            case (isset($info['method'])):
                $class = $info['method']['class'];

                /**
                 * @todo Should this be done? or should we assume the class is 
                 * loaded? 
                 */
                if (!class_exists($class)) {
                    Zend::loadClass($class);
                }

                $call  = $info['method']['method'];

                try {
                    if (isset($info['method']['params'])) {
                        $obj = new $class($info['method']['params']);
                    } else {
                        $obj = new $class();
                    }
                } catch (Exception $e) {
                    throw new Zend_XmlRpc_Server_Exception('Error calling method', 500);
                }
                $callback = array($obj, $call);
                break;
            case (isset($info['static'])):
                $class = $info['static']['class'];
                if (!class_exists($class)) {
                    Zend::loadClass($class);
                }

                $callback = array($class, $info['static']['method']);
                break;
            default:
                throw new Zend_XmlRpc_Server_Exception('Method missing implementation', 501);
                break;
        }

        if (!is_callable($callback)) {
            throw new Zend_XmlRpc_Server_Exception('Invalid method implementation', 502);
        }

        return call_user_func_array($callback, $args);
    }

    /**
     * Handle an xmlrpc call
     *
     * @param string $request Optional; XMLRPC request XML
     * @return string
     */
    public function handle($request = false) 
    {
        $this->_responseXml = null;
        $this->_response    = null;

        // Get request
        if (!$request) {
            try {
                // Get POST'd xml
                $request = $this->_getRequest();
            } catch (Exception $e) {
                $this->_responseXml = $this->fault($e)->__toString();
            }
        }
        $this->_requestXml = iconv('', 'UTF-8', $request);

        // Parse request
        try {
            $this->_parseXmlRequest();
        } catch (Exception $e) {
            $this->_responseXml = $this->fault($e)->__toString();
        }

        // Dispatch
        if (null === $this->_responseXml) {
            // Only dispatch if we have a vaild request
            try {
                $this->_response = $this->_handle($this->_method, $this->_args);
            } catch (Exception $e) {
                $this->_responseXml = $this->fault($e)->__toString();
            }
        }

        // Build response
        if (null === $this->_responseXml) {
            // Only build response if a fault didn't occur
            $value = Zend_XmlRpc_Value::getXmlRpcValue($this->_response);
            $this->_responseXml = $this->_buildResponse($value);
        }

        // Send response
        if (!headers_sent()) {
            header('Content-Type: application/xml; charset=iso-8859-1');
        }
        return $this->_responseXml;
    }

    /**
     * Get xmlrpc request
     *
     * Gets and returns the XML request string from php://input
     * 
     * @return void
     * @throws Zend_XmlRpc_Server_Exception
     */
    protected function _getRequest()
    {
        $fh = fopen('php://input', 'r');
        if (!$fh) {
            throw new Zend_XmlRpc_Server_Exception('Unable to read request', 500);
        }

        $xml = '';
        while (!feof($fh)) {
            $xml .= fgets($fh);
        }
        fclose($fh);

        return $xml;
    }

    /**
     * Parse the XML from an xmlrpc request
     * 
     * @param string $xml
     * @return void
     * @throws Zend_XmlRpc_Server_Exception
     */
    protected function _parseXmlRequest()
    {
        try {
            $xml = @new SimpleXMLElement($this->_requestXml);
        } catch (Exception $e) {
            // Not valid XML
            throw new Zend_XmlRpc_Server_Exception('Failed to parse request: ' .  $e->getMessage(), 500);
        }

        // Check for method name
        if (empty($xml->methodName)) {
            // Missing method name
            throw new Zend_XmlRpc_Server_Exception('Invalid request, no method passed; request must contain a \'methodName\' tag', 401);
        }

        $this->_method = (string) $xml->methodName;

        // Check for parameters
        if (!empty($xml->params)) {
            $args = array();
            $e = false;
            foreach ($xml->params->children() as $param) {
                if (! $param->value instanceof SimpleXMLElement) {
                    $e = 'Param must contain a value';
                    break;
                }

                $args[] = Zend_XmlRpc_Value::getXmlRpcValue($param->value, Zend_XmlRpc_Value::XML_STRING)->getValue();
            }

            if ($e) {
                throw new Zend_XmlRpc_Server_Exception($e, 401);
            }

            $this->_args = $args;
        } else {
            $this->_args = null;
        }
    }

    /**
     * Build the XML response
     *
     * @param Zend_XmlRpc_Value $value
     * @return string
     */
    protected function _buildResponse($value) 
    {
        $dom = new DOMDocument('1.0', 'ISO-8859-1');
        $response = $dom->appendChild($dom->createElement('methodResponse'));
        $params   = $response->appendChild($dom->createElement('params'));
        $param    = $params->appendChild($dom->createElement('param'));

        $param->appendChild($dom->importNode($value->getAsDOM(), true));

        return $dom->saveXML();
    }

    /**
     * Returns the callback dispatch table
     *
     * Returns the callback dispatch table.
     * 
     * @return array
     */
    public function getCallbacks()
    {
        return $this->_methods;
    }

    /**
     * Returns a list of registered methods
     * 
     * @return array
     */
    public function getFunctions()
    {
        return array_keys($this->_methods);
    }

    /**
     * Returns last request as XML
     * 
     * @return string
     */
    public function getLastRequestXML()
    {
        return $this->_requestXml;
    }

    /**
     * Returns last response as XML
     * 
     * @return string
     */
    public function getLastResponseXML()
    {
        return $this->_responseXml;
    }

    /**
     * Returns last response as PHP values
     * 
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->_response;
    }

    /**
     * Returns last request as PHP value
     *
     * Returns last request as an array with the following structure:
     * - 'methodName' => method name called
     * - 'params' => array of params passed in request
     * 
     * @return array
     */
    public function getLastRequest()
    {
        return array(
            'methodName' => $this->_method,
            'params'     => $this->_args
        );
    }

    /**
     * List all available XMLRPC methods
     *
     * Returns an array of methods.
     * 
     * @return array
     */
    public function listMethods()
    {
        return array_keys($this->_methods);
    }

    /**
     * Display help message for an XMLRPC method
     * 
     * @param string $method
     * @return string
     */
    public function methodHelp($method)
    {
        if (isset($this->_methods[$method]) 
            && isset($this->_methods[$method]['methodHelp']))
        {
            return $this->_methods[$method]['methodHelp'];
        }

        return '';
    }

    /**
     * Return a method signature
     * 
     * @param string $method
     * @return array
     */
    public function methodSignature($method)
    {
        if (isset($this->_methods[$method])
            && isset($this->_methods[$method]['signatures']))
        {
            return $this->_methods[$method]['signatures'];
        }

        return array();
    }

    /**
     * Multicall - boxcar feature of XML-RPC for calling multiple methods
     * in a single request.
     *
     * Expects a an array of structs representing method calls, each element
     * having the keys:
     * - methodName
     * - params
     *
     * Returns an array of responses, one for each method called, with the value
     * returned by the method. If an error occurs for a given method, returns a
     * struct with a fault response.
     *
     * @see http://www.xmlrpc.com/discuss/msgReader$1208
     * @param array
     * @return array
     */
    public function multicall($methods) 
    {
        $responses = array();
        foreach ($methods as $method) {
            $fault = false;
            if (!is_array($method)) {
                $fault = $this->fault('system.multicall expects each method to be a struct', 601);
            } elseif (!isset($method['methodName'])) {
                $fault = $this->fault('Missing methodName', 602);
                $responses[] = array(
                    'faultCode'   => $fault->getCode(),
                    'faultString' => $fault->getMessage()
                );
                continue;
            } elseif (!isset($method['params'])) {
                $fault = $this->fault('Missing params', 603);
            } elseif (!is_array($method['params'])) {
                $fault = $this->fault('Params must be an array', 604);
            } else {
                $methodName = $method['methodName'];
                if ('system.multicall' == $methodName) {
                    // don't allow recursive calls to multicall
                    $fault = $this->fault('Recursive system.multicall forbidden', 605);
                } else {
                    $args = $method['params'];
                }
            }

            if (!$fault) {
                try {
                    $response = $this->_handle($methodName, $args);
                    $responses[] = $response;
                } catch (Exception $e) {
                    $fault = $this->fault($e);
                }
            }

            if ($fault) {
                $responses[] = array(
                    'faultCode'   => $fault->getCode(),
                    'faultString' => $fault->getMessage()
                );
            }
        }

        return $responses;
    }
}
