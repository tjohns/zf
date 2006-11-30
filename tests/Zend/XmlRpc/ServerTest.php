<?php
require_once 'Zend/XmlRpc/Server.php';
require_once 'Zend/XmlRpc/Request.php';
require_once 'Zend/XmlRpc/Response.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test case for Zend_XmlRpc_Server
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_XmlRpc_ServerTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_XmlRpc_Server object
     * @var Zend_XmlRpc_Server
     */
    protected $_server;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_server = new Zend_XmlRpc_Server();
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        unset($this->_server);
    }

    /**
     * __construct() test
     *
     * Call as method call 
     *
     * Returns: void 
     */
    public function test__construct()
    {
        $this->assertTrue($this->_server instanceof Zend_XmlRpc_Server);
    }

    /**
     * addFunction() test
     *
     * Call as method call 
     *
     * Expects:
     * - function: 
     * - namespace: Optional; has default; 
     * 
     * Returns: void 
     */
    public function testAddFunction()
    {
        try {
            $this->_server->addFunction('Zend_XmlRpc_Server_testFunction', 'zsr');
        } catch (Exception $e) {
            $this->fail('Attachment should have worked');
        }

        $methods = $this->_server->listMethods();
        $this->assertTrue(in_array('zsr.Zend_XmlRpc_Server_testFunction', $methods));

        try {
            $this->_server->addFunction('nosuchfunction');
            $this->fail('nosuchfunction() should not exist and should throw an exception');
        } catch (Exception $e) {
            // do nothing
        }

        $server = new Zend_XmlRpc_Server();
        try {
            $server->addFunction(
                array(
                    'Zend_XmlRpc_Server_testFunction',
                    'Zend_XmlRpc_Server_testFunction2',
                ),
                'zsr'
            );
        } catch (Exception $e) {
            $this->fail('Error attaching array of functions: ' . $e->getMessage());
        }
        $methods = $server->listMethods();
        $this->assertTrue(in_array('zsr.Zend_XmlRpc_Server_testFunction', $methods));
        $this->assertTrue(in_array('zsr.Zend_XmlRpc_Server_testFunction2', $methods));
    }

    /**
     * get/loadFunctions() test
     */
    public function testFunctions()
    {
        try {
            $this->_server->addFunction(
                array(
                    'Zend_XmlRpc_Server_testFunction',
                    'Zend_XmlRpc_Server_testFunction2',
                ),
                'zsr'
            );
        } catch (Exception $e) {
            $this->fail('Error attaching functions: ' . $e->getMessage());
        }

        $expected = $this->_server->listMethods();

        $functions = $this->_server->getFunctions();
        $server = new Zend_XmlRpc_Server();
        $server->loadFunctions($functions);
        $actual = $server->listMethods();

        $this->assertSame($expected, $actual);
    }

    /**
     * setClass() test
     */
    public function testSetClass()
    {
        $this->_server->setClass('Zend_XmlRpc_Server_testClass', 'test');
        $methods = $this->_server->listMethods();
        $this->assertTrue(in_array('test.test1', $methods));
        $this->assertTrue(in_array('test.test2', $methods));
        $this->assertFalse(in_array('test._test3', $methods));
        $this->assertFalse(in_array('test.__construct', $methods));
    }

    /**
     * fault() test
     */
    public function testFault()
    {
        $fault = $this->_server->fault('This is a fault', 411);
        $this->assertTrue($fault instanceof Zend_XmlRpc_Server_Fault);
        $this->assertEquals(411, $fault->getCode());
        $this->assertEquals('This is a fault', $fault->getMessage());

        $fault = $this->_server->fault(new Zend_XmlRpc_Server_Exception('Exception fault', 511));
        $this->assertTrue($fault instanceof Zend_XmlRpc_Server_Fault);
        $this->assertEquals(511, $fault->getCode());
        $this->assertEquals('Exception fault', $fault->getMessage());
    }

    /**
     * handle() test
     *
     * Call as method call 
     *
     * Expects:
     * - request: Optional; 
     * 
     * Returns: Zend_XmlRpc_Response|Zend_XmlRpc_Fault 
     */
    public function testHandle()
    {
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.listMethods');
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Response);
        $return = $response->getReturnValue();
        $this->assertTrue(is_array($return));
        $this->assertTrue(in_array('system.multicall', $return));
    }

    /**
     * Test that only calling methods using a valid parameter signature works
     */
    public function testHandle2()
    {
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.methodHelp');
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Fault);
        $this->assertEquals(623, $response->getCode());
    }

    /**
     * setResponseClass() test
     *
     * Call as method call 
     *
     * Expects:
     * - class: 
     * 
     * Returns: boolean 
     */
    public function testSetResponseClass()
    {
        $this->_server->setResponseClass('Zend_XmlRpc_Server_testResponse');
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.listMethods');
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Response);
        $this->assertTrue($response instanceof Zend_XmlRpc_Server_testResponse);
    }

    /**
     * listMethods() test
     *
     * Call as method call 
     *
     * Returns: array 
     */
    public function testListMethods()
    {
        $methods = $this->_server->listMethods();
        $this->assertTrue(is_array($methods));
        $this->assertTrue(in_array('system.listMethods', $methods));
        $this->assertTrue(in_array('system.methodHelp', $methods));
        $this->assertTrue(in_array('system.methodSignature', $methods));
        $this->assertTrue(in_array('system.multicall', $methods));
    }

    /**
     * methodHelp() test
     *
     * Call as method call 
     *
     * Expects:
     * - method: 
     * 
     * Returns: string 
     */
    public function testMethodHelp()
    {
        $help = $this->_server->methodHelp('system.listMethods');
        $this->assertContains('all available XMLRPC methods', $help);
    }

    /**
     * methodSignature() test
     *
     * Call as method call 
     *
     * Expects:
     * - method: 
     * 
     * Returns: array 
     */
    public function testMethodSignature()
    {
        $sig = $this->_server->methodSignature('system.methodSignature');
        $this->assertTrue(is_array($sig));
        $this->assertEquals(1, count($sig), var_export($sig, 1));
    }

    /**
     * multicall() test
     *
     * Call as method call 
     *
     * Expects:
     * - methods: 
     * 
     * Returns: array 
     */
    public function testMulticall()
    {
        $struct = array(
            array(
                'methodName' => 'system.listMethods',
                'params' => array()
            ),
            array(
                'methodName' => 'system.methodHelp',
                'params' => array('system.multicall')
            )
        );
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.multicall');
        $request->addParam($struct);
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Response, $response->__toString());
        $returns = $response->getReturnValue();
        $this->assertTrue(is_array($returns));
        $this->assertEquals(2, count($returns));
        $this->assertTrue(is_array($returns[0]), var_export($returns[0], 1));
        $this->assertTrue(is_string($returns[1]), var_export($returns[1], 1));
    }

    /**
     * Test get/setEncoding()
     */
    public function testGetSetEncoding()
    {
        $this->assertEquals('UTF-8', $this->_server->getEncoding());
        $this->_server->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->_server->getEncoding());
    }

    /**
     * Test request/response encoding 
     */
    public function testRequestResponseEncoding()
    {
        $response = $this->_server->handle();
        $request  = $this->_server->getRequest();

        $this->assertEquals('UTF-8', $request->getEncoding());
        $this->assertEquals('UTF-8', $response->getEncoding());
    }

    /**
     * Test request/response encoding (alternate encoding)
     */
    public function testRequestResponseEncoding2()
    {
        $this->_server->setEncoding('ISO-8859-1');
        $response = $this->_server->handle();
        $request  = $this->_server->getRequest();

        $this->assertEquals('ISO-8859-1', $request->getEncoding());
        $this->assertEquals('ISO-8859-1', $response->getEncoding());
    }
}

/**
 * Zend_XmlRpc_Server_testFunction 
 *
 * Function for use with xmlrpc server unit tests
 * 
 * @param array $var1 
 * @param string $var2 
 * @return string
 */
function Zend_XmlRpc_Server_testFunction($var1, $var2 = 'optional')
{
    return $var2 . ': ' . implode(',', (array) $var1);
}

/**
 * Zend_XmlRpc_Server_testFunction2
 *
 * Function for use with xmlrpc server unit tests
 * 
 * @return string
 */
function Zend_XmlRpc_Server_testFunction2()
{
    return 'function2';
}


class Zend_XmlRpc_Server_testClass
{
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Test1 
     *
     * Returns 'String: ' . $string
     * 
     * @param string $string 
     * @return string
     */
    public function test1($string)
    {
        return 'String: ' . (string) $string;
    }

    /**
     * Test2 
     *
     * Returns imploded array
     * 
     * @param array $array 
     * @return string
     */
    public static function test2($array)
    {
        return implode('; ', (array) $array);
    }

    /**
     * Test3 
     *
     * Should not be available...
     * 
     * @return void
     */
    protected function _test3()
    {
    }
}

class Zend_XmlRpc_Server_testResponse extends Zend_XmlRpc_Response
{
}
