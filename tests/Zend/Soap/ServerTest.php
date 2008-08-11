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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** PHPUnit Test Case */
require_once "PHPUnit/Framework/TestCase.php";

/** Zend_Soap_Server */
require_once 'Zend/Soap/Server.php';

/**
 * Zend_Soap_Server
 *
 * @category   Zend
 * @package    UnitTests
 * @uses       Zend_Server_Interface
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Soap_ServerTest extends PHPUnit_Framework_TestCase
{
    public function testSetOptions()
    {
    	if (!extension_loaded('soap')) {
    	   $this->markTestSkipped('SOAP Extension is not loaded');
    	}

        $server = new Zend_Soap_Server();

        $this->assertTrue($server->getOptions() == array('soap_version' => SOAP_1_2));

        $options = array('soap_version' => SOAP_1_1,
                         'actor' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                         'classmap' => array('TestData1' => 'Zend_Soap_Server_TestData1',
                                             'TestData2' => 'Zend_Soap_Server_TestData2',),
                         'encoding' => 'ISO-8859-1',
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $server->setOptions($options);

        $this->assertTrue($server->getOptions() == $options);
    }

    public function testGetOptions()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertTrue($server->getOptions() == array('soap_version' => SOAP_1_2));

        $options = array('soap_version' => SOAP_1_1,
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $server->setOptions($options);

        $this->assertTrue($server->getOptions() == $options);
    }

    public function testEncoding()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getEncoding());
        $server->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $server->getEncoding());

        try {
            $server->setEncoding(array('UTF-8'));
            $this->fail('Non-string encoding values should fail');
        } catch (Exception $e) {
            // success
        }
    }

    public function testSoapVersion()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertEquals(SOAP_1_2, $server->getSoapVersion());
        $server->setSoapVersion(SOAP_1_1);
        $this->assertEquals(SOAP_1_1, $server->getSoapVersion());
        try {
            $server->setSoapVersion('bogus');
            $this->fail('Invalid soap versions should fail');
        } catch (Exception $e)  {
            // success
        }
    }

    public function testValidateUrn()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        try {
            $server->validateUrn('bogosity');
            $this->fail('URNs without schemes should fail');
        } catch (Exception $e) {
            // success
        }

        $this->assertTrue($server->validateUrn('http://framework.zend.com/'));
        $this->assertTrue($server->validateUrn('urn:soapHandler/GetOpt'));
    }

    public function testSetActor()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getActor());
        $server->setActor('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getActor());
        try {
            $server->setActor('bogus');
            $this->fail('Invalid actor should fail');
        } catch (Exception $e)  {
            // success
        }
    }

    public function testGetActor()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getActor());
        $server->setActor('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getActor());
    }

    public function testSetUri()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getUri());
        $server->setUri('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getUri());
        try {
            $server->setUri('bogus');
            $this->fail('Invalid URI should fail');
        } catch (Exception $e)  {
            // success
        }
    }

    public function testGetUri()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getUri());
        $server->setUri('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getUri());
    }

    public function testSetClassmap()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $classmap = array('TestData1' => 'Zend_Soap_Server_TestData1',
                          'TestData2' => 'Zend_Soap_Server_TestData2');

        $this->assertNull($server->getClassmap());
        $server->setClassmap($classmap);
        $this->assertTrue($classmap == $server->getClassmap());
        try {
            $server->setClassmap('bogus');
            $this->fail('Classmap which is not an array should fail');
        } catch (Exception $e)  {
            // success
        }
        try {
            $server->setClassmap(array('soapTypeName', 'bogusClassName'));
            $this->fail('Invalid class within classmap should fail');
        } catch (Exception $e)  {
            // success
        }
    }

    public function testGetClassmap()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $classmap = array('TestData1' => 'Zend_Soap_Server_TestData1',
                          'TestData2' => 'Zend_Soap_Server_TestData2');

        $this->assertNull($server->getClassmap());
        $server->setClassmap($classmap);
        $this->assertTrue($classmap == $server->getClassmap());
    }

    public function testSetWsdl()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getWsdl());
        $server->setWsdl(dirname(__FILE__).'/_files/wsdl_example.wsdl');
        $this->assertEquals(dirname(__FILE__).'/_files/wsdl_example.wsdl', $server->getWsdl());
        try {
            $server->setWsdl(dirname(__FILE__).'/_files/bogus.wsdl');
            $this->fail('Invalid WSDL URI or PATH should fail');
        } catch (Exception $e)  {
            // success
        }
    }

    public function testGetWsdl()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getWsdl());
        $server->setWsdl(dirname(__FILE__).'/_files/wsdl_example.wsdl');
        $this->assertEquals(dirname(__FILE__).'/_files/wsdl_example.wsdl', $server->getWsdl());
    }

    public function testAddFunction()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        try {
            $server->addFunction('bogus_function');
            $this->fail('Invalid function should fail');
        } catch (Exception $e)  {
            // success
        }

        // Correct function should pass
        $server->addFunction('Zend_Soap_Server_TestFunc1');

        // Array of correct functions should pass
        $functions = array('Zend_Soap_Server_TestFunc2',
                           'Zend_Soap_Server_TestFunc3',
                           'Zend_Soap_Server_TestFunc4');
        $server->addFunction($functions);

        try {
	        $functions1 = array('Zend_Soap_Server_TestFunc5',
	                            'bogus_function',
	                            'Zend_Soap_Server_TestFunc6');
        	$server->addFunction('bogus_function');
            $this->fail('Invalid function within a set of functions should fail');
        } catch (Exception $e)  {
            // success
        }

        try {
            $server->addFunction(126);
            $this->fail('Invalid value should fail');
        } catch (Exception $e)  {
            // success
        }

        // SOAP_FUNCTIONS_ALL as a value should pass
        $server->addFunction(SOAP_FUNCTIONS_ALL);
    }

    public function testSetClass()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        try {
            $server->setClass(465);
            $this->fail('Non-string value should fail');
        } catch (Exception $e)  {
            // success
        }
        try {
            $server->setClass('Zend_Soap_Server_Test_BogusClass');
            $this->fail('Invalid class should fail');
        } catch (Exception $e)  {
            // success
        }

        // Correct class name should pass
        $server->setClass('Zend_Soap_Server_TestClass');

        try {
            $server->setClass('Zend_Soap_Server_TestClass');
            $this->fail('setClass() should pass only once');
        } catch (Exception $e)  {
            // success
        }
    }

    public function testGetFunctions()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $server->addFunction('Zend_Soap_Server_TestFunc1');

        $functions  =  array('Zend_Soap_Server_TestFunc2',
                             'Zend_Soap_Server_TestFunc3',
                             'Zend_Soap_Server_TestFunc4');
        $server->addFunction($functions);

        $functions  =  array('Zend_Soap_Server_TestFunc3',
                             'Zend_Soap_Server_TestFunc5',
                             'Zend_Soap_Server_TestFunc6');
        $server->addFunction($functions);

        $this->assertTrue($server->getFunctions() == array('Zend_Soap_Server_TestFunc1',
                                                           'Zend_Soap_Server_TestFunc2',
                                                           'Zend_Soap_Server_TestFunc3',
                                                           'Zend_Soap_Server_TestFunc4',
                                                           'Zend_Soap_Server_TestFunc5',
							                               'Zend_Soap_Server_TestFunc6'));
    }

    public function testSetPersistence()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getPersistence());
        $server->setPersistence(SOAP_PERSISTENCE_SESSION);
        $this->assertEquals(SOAP_PERSISTENCE_SESSION, $server->getPersistence());
        try {
            $server->setSoapVersion('bogus');
            $this->fail('Invalid soap versions should fail');
        } catch (Exception $e)  {
            // success
        }

        $server->setPersistence(SOAP_PERSISTENCE_REQUEST);
        $this->assertEquals(SOAP_PERSISTENCE_REQUEST, $server->getPersistence());
    }

    public function testGetPersistence()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertNull($server->getPersistence());
        $server->setPersistence(SOAP_PERSISTENCE_SESSION);
        $this->assertEquals(SOAP_PERSISTENCE_SESSION, $server->getPersistence());
    }

    public function testGetLastRequest()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        $server->setClass('Zend_Soap_Server_TestClass');

        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . PHP_EOL;

        $response = $server->handle($request);

        $this->assertEquals($request, $server->getLastRequest());
    }

    public function testSetReturnResponse()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertFalse($server->getReturnResponse());

        $server->setReturnResponse(true);
        $this->assertTrue($server->getReturnResponse());

        $server->setReturnResponse(false);
        $this->assertFalse($server->getReturnResponse());
    }

    public function testGetReturnResponse()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        $this->assertFalse($server->getReturnResponse());

        $server->setReturnResponse(true);
        $this->assertTrue($server->getReturnResponse());
    }

    public function testGetLastResponse()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        $server->setClass('Zend_Soap_Server_TestClass');

        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . PHP_EOL;

        $expectedResponse =
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2Response>'
          .             '<return xsi:type="xsd:string">Hello World!</return>'
          .         '</ns1:testFunc2Response>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . PHP_EOL;

        $server->handle($request);

        $this->assertEquals($expectedResponse, $server->getLastResponse());
    }

    public function testHandle()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));

        $server->setClass('Zend_Soap_Server_TestClass');

        $localClient = new Zend_Soap_Server_TestLocalSoapClient($server,
                                                                null,
                                                                array('location'=>'test://',
                                                                      'uri'=>'http://framework.zend.com'));

        // Local SOAP client call automatically invokes handle method of the provided SOAP server
        $this->assertEquals('Hello World!', $localClient->testFunc2('World'));


        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . PHP_EOL;

        $expectedResponse =
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2Response>'
          .             '<return xsi:type="xsd:string">Hello World!</return>'
          .         '</ns1:testFunc2Response>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . PHP_EOL;

        $server1 = new Zend_Soap_Server();
        $server1->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));

        $server1->setClass('Zend_Soap_Server_TestClass');
        $server1->setReturnResponse(true);

        $this->assertEquals($expectedResponse, $server1->handle($request));
    }

    /**
     * @todo Implement testRegisterFaultException().
     */
    public function testRegisterFaultException()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDeregisterFaultException().
     */
    public function testDeregisterFaultException()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetFaultExceptions().
     */
    public function testGetFaultExceptions()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testFault().
     */
    public function testFault()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testHandlePhpErrors().
     */
    public function testHandlePhpErrors()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }

    	$server = new Zend_Soap_Server();

        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}


if (extension_loaded('soap')) {

/** Local SOAP client */
class Zend_Soap_Server_TestLocalSoapClient extends SoapClient {
	/**
	 * Server object
	 *
	 * @var Zend_Soap_Server
	 */
	public $server;

	/**
	 * Local client constructor
	 *
	 * @param Zend_Soap_Server $server
	 * @param string $wsdl
	 * @param array $options
	 */
    function __construct(Zend_Soap_Server $server, $wsdl, $options) {
        $this->server = $server;
    	parent::__construct($wsdl, $options);
    }

    function __doRequest($request, $location, $action, $version) {
        ob_start();
        $this->server->handle($request);
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }
}

}


/** Test Class */
class Zend_Soap_Server_TestClass {
    /**
     * Test Function 1
     *
     * @return string
     */
    function testFunc1()
    {
        return "Hello World";
    }

    /**
     * Test Function 2
     *
     * @param string $who Some Arg
     * @return string
     */
    function testFunc2($who)
    {
        return "Hello $who!";
    }

    /**
     * Test Function 3
     *
     * @param string $who Some Arg
     * @param int $when Some
     * @return string
     */
    function testFunc3($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 4
     *
     * @return string
     */
    static function testFunc4()
    {
        return "I'm Static!";
    }
}


/** Test class 2 */
class Zend_Soap_Server_TestData1 {
    /**
     * Property1
     *
     * @var string
     */
     public $property1;

    /**
     * Property2
     *
     * @var float
     */
     public $property2;
}

/** Test class 2 */
class Zend_Soap_Server_TestData2 {
    /**
     * Property1
     *
     * @var integer
     */
     public $property1;

    /**
     * Property1
     *
     * @var float
     */
     public $property2;
}


/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function Zend_Soap_Server_TestFunc1($who)
{
    return "Hello $who";
}

/**
 * Test Function 2
 */
function Zend_Soap_Server_TestFunc2()
{
    return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function Zend_Soap_Server_TestFunc3()
{
    return false;
}

/**
 * Return true
 *
 * @return bool
 */
function Zend_Soap_Server_TestFunc4()
{
    return true;
}

/**
 * Return integer
 *
 * @return int
 */
function Zend_Soap_Server_TestFunc5()
{
    return 123;
}

/**
 * Return string
 *
 * @return string
 */
function Zend_Soap_Server_TestFunc6()
{
    return "string";
}

