<?php
/**
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 */

/**
 * Zend_XmlRpc_Server
 */
require_once 'Zend/XmlRpc/Server.php';

/**
 * PHPUnit2 Test Case
 */
require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * Test cases for Zend_XmlRpc_Server
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 */
class Zend_XmlRpc_ServerTest extends PHPUnit2_Framework_TestCase 
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
     * addFunction() test
     */
    public function testAddFunction()
    {
        $this->_server->addFunction('zxrs_func1', 'func1');
        $methods = $this->_server->getCallbacks();
        $this->assertTrue(isset($methods['func1.zxrs_func1']));
        $this->assertTrue(isset($methods['func1.zxrs_func1']['function']));
        $this->assertEquals('zxrs_func1', $methods['func1.zxrs_func1']['function']);
        $this->assertTrue(isset($methods['func1.zxrs_func1']['methodHelp']));
        $this->assertContains('XMLRPC function 1', $methods['func1.zxrs_func1']['methodHelp']);
    }

    /**
     * loadArray() test
     */
    public function testLoadArray()
    {
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $methods= $this->_server->getCallbacks();

        $server = new Zend_XmlRpc_Server();
        try {
            $server->loadArray($methods);
        } catch (Exception $e) {
            $this->fail('Failed loading table from array: ' . $e->getMessage());
        }

        $received = $server->getCallbacks();
        $diff = array_diff($methods, $received);
        $this->assertTrue(empty($diff), var_export($diff, 1));
    }

    /**
     * setClass() test
     */
    public function testSetClass()
    {
        $need = array(
            'domain1.getVars',
            'domain1.staticCall',
            'domain1.withVars',
            'domain1.staticVars'
        );
        $static = array(
            'domain1.staticCall',
            'domain1.staticVars'
        );

        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $methods = $this->_server->getCallbacks();
        foreach ($need as $method) {
            $this->assertTrue(isset($methods[$method]));
            if (in_array($method, $static)) {
                $this->assertTrue(isset($methods[$method]['static']));
            } else {
                $this->assertTrue(isset($methods[$method]['method']));
            }
        }

        $this->_server->setClass('zxrs_test_methods', 'domain2', false);
        $methods = $this->_server->getCallbacks();
        $this->assertFalse(isset($methods['domain2.getVars']));
        $this->assertFalse(isset($methods['domain2.withVars']));
        $this->assertTrue(isset($methods['domain2.staticCall']));
        $this->assertTrue(isset($methods['domain2.staticVars']));
    }

    /**
     * fault() test
     */
    public function testFault()
    {
        $fault = $this->_server->fault('Testing errors', 700);
        $this->assertTrue($fault instanceof Zend_XmlRpc_Server_Fault);
        $this->assertEquals('Testing errors', $fault->getMessage());
        $this->assertEquals(700, $fault->getCode());

        $e = new zxrs1_exception('Testing errors', 700);
        $fault = $this->_server->fault($e);
        $this->assertTrue($fault instanceof Zend_XmlRpc_Server_Fault);
        $this->assertEquals('Unknown error', $fault->getMessage());
        $this->assertEquals(404, $fault->getCode());

        Zend_XmlRpc_Server_Fault::attachFaultException('zxrs1_exception');
        $fault = $this->_server->fault($e);
        $this->assertTrue($fault instanceof Zend_XmlRpc_Server_Fault);
        $this->assertEquals('Testing errors', $fault->getMessage());
        $this->assertEquals(700, $fault->getCode());
        Zend_XmlRpc_Server_Fault::detachFaultException('zxrs1_exception');
    }

    /**
     * handle() test
     */
    public function testHandle()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>domain1.staticCall</methodName>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $this->assertContains('<string>statically</string>', $response);
    }

    /**
     * Test handle()
     *
     * test what happens with an invalid method
     */
    public function testHandle2()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>bogusMethod</methodName>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $this->assertContains('<fault>', $response);
        $this->assertContains('<int>404</int>', $response);
    }

    /**
     * Test handle()
     * 
     * Test what happens when calling an object instance method when vars have 
     * been set.
     */
    public function testHandle3()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>domain1.getVars</methodName>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1', true, 'PHP Hypertext Preprocessor', 'Open Source', 'Zend');
        $response = $this->_server->handle($request);

        $this->assertContains('<string>PHP Hypertext Preprocessor</string>', $response);
        $this->assertContains('<string>Open Source</string>', $response);
        $this->assertContains('<string>Zend</string>', $response);
    }

    /**
     * Test handle()
     *
     * Test passing arguments to a method
     * 
     * @access public
     * @return void
     */
    public function testHandle4()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>domain1.withVars</methodName>
    <params>
        <param>
            <value><string>This is the Title</string></value>
        </param>
        <param><value><array>
            <data>
                <value><string>First arg</string></value>
                <value><string>Second arg</string></value>
                <value><string>Third arg</string></value>
            </data>
        </array></value></param>
    </params>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $this->assertContains('<string>This is the Title', $response);
        $this->assertContains('First arg', $response);
        $this->assertContains('Second arg', $response);
        $this->assertContains('Third arg', $response);
        $this->assertContains('</string>', $response);
    }

    /**
     * getCallbacks() test
     */
    public function testGetCallbacks()
    {
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $methods = $this->_server->getCallbacks();
        $need = array(
            'system.listMethods',
            'system.methodHelp',
            'system.methodSignature',
            'system.multicall',
            'domain1.getVars',
            'domain1.staticCall',
            'domain1.withVars',
            'domain1.staticVars'
        );

        $diff = array_diff($need, array_keys($methods));
        $this->assertTrue(empty($diff));
    }

    /**
     * getLastRequestXML() test
     */
    public function testGetLastRequestXML()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>domain1.staticCall</methodName>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $this->assertEquals($request, $this->_server->getLastRequestXML());
    }

    /**
     * getLastResponseXML() test
     */
    public function testGetLastResponseXML()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>domain1.staticCall</methodName>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $this->assertSame($response, $this->_server->getLastResponseXML());
    }

    /**
     * getLastResponse() test
     */
    public function testGetLastResponse()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>domain1.staticCall</methodName>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $this->assertEquals('statically', $this->_server->getLastResponse());
    }

    /**
     * getLastRequest() test
     */
    public function testGetLastRequest()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>domain1.withVars</methodName>
    <params>
        <param>
            <value><string>This is the Title</string></value>
        </param>
        <param><value><array>
            <data>
                <value><string>First arg</string></value>
                <value><string>Second arg</string></value>
                <value><string>Third arg</string></value>
            </data>
        </array></value></param>
    </params>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $params = array(
            'This is the Title',
            array('First arg', 'Second arg', 'Third arg')
        );

        $lastRequest = $this->_server->getLastRequest();
        $this->assertSame('domain1.withVars', $lastRequest['methodName']);
        $this->assertSame($params, $lastRequest['params']);
    }

    /**
     * listMethods() test
     */
    public function testListMethods()
    {
        $expected = array(
            'system.listMethods',
            'system.methodHelp',
            'system.methodSignature',
            'system.multicall'
        );

        $actual = $this->_server->listMethods();
        $this->assertEquals($expected, $actual, var_export($actual, 1));
    }

    /**
     * methodHelp() test
     */
    public function testMethodHelp()
    {
        $methodHelp = $this->_server->methodHelp('system.methodHelp');
        $this->assertContains('Display help message for an XMLRPC method', $methodHelp);
    }

    /**
     * methodSignature() test
     */
    public function testMethodSignature()
    {
        $sig = $this->_server->methodSignature('system.methodSignature');
        $expected = array(array('array', 'string'));
        $this->assertEquals($expected, $sig);
    }

    /**
     * multicall() test
     *
     * @todo Determine how to parse response into constituent array
     */
    public function testMulticall()
    {
        $request =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>system.multicall</methodName>
    <params>
        <param><value><array><data>
            <value><struct>
                <member>
                    <name>methodName</name>
                    <value><string>domain1.staticCall</string></value>
                </member>
                <member>
                    <name>params</name>
                    <value><array><data></data></array></value>
                </member>
            </struct></value>
            <value><struct>
                <member>
                    <name>methodName</name>
                    <value><string>domain1.staticVars</string></value>
                </member>
                <member>
                    <name>params</name>
                    <value><array><data>
                        <value><string>Title String</string></value>
                        <value><array><data>
                            <value><string>First arg</string></value>
                            <value><string>Second arg</string></value>
                            <value><string>Third arg</string></value>
                        </data></array></value>
                    </data></array></value>
                </member>
            </struct></value>
            <value><struct>
                <member>
                    <name>methodName</name>
                    <value><string>bogus</string></value>
                </member>
                <member>
                    <name>params</name>
                    <value><array><data></data></array></value>
                </member>
            </struct></value>
        </data></array></value></param>
    </params>
</methodCall>
XML;
        $this->_server->setClass('zxrs_test_methods', 'domain1');
        $response = $this->_server->handle($request);

        $this->assertContains('statically', $response);
        $this->assertContains('Second arg', $response);
        $this->assertContains('faultString', $response);
    }
}

/**
 * XMLRPC function 1
 * 
 * @param string $string 
 * @param struct $struct 
 * @return string
 */
function zxrs_func1($string, $struct)
{
    $return = $string. "\n";
    foreach ($struct as $key => $value) {
        $return .= "$key: $value\n";
    }

    return $return;
}

/**
 * Sample exception class for testing faults
 */
class zxrs1_exception extends Exception {}

/**
 * Sample exception class for testing faults
 */
class zxrs2_exception extends Exception {}

/**
 * Class for testing attaching class methods
 */
class zxrs_test_methods
{
    protected $_vars;

    public function __construct($vars = false)
    {
        $this->_vars = $vars;
    }

    /**
     * Retrieve internal vars
     * 
     * @return false|array
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * Try a static method
     * 
     * @return string
     */
    public static function staticCall()
    {
        return 'statically';
    }

    /**
     * Public method with vars
     * 
     * @param string $string 
     * @param array $array 
     * @return string
     */
    public function withVars($string, $array)
    {
        $return = $string . "\n";
        foreach ($array as $string) {
            $return .= $string . "\n";
        }

        return $return;
    }

    /**
     * Static method with vars
     * 
     * @param string $string 
     * @param array $array 
     * @return array
     */
    public static function staticVars($string, $array)
    {
        array_unshift($array, $string);
        return $array;
    }
}

