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

// Call Zend_Soap_ServerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Soap_ServerTest::main");

    $baseZfPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..';

    set_include_path(
        '.' 
        . PATH_SEPARATOR . $baseZfPath . DIRECTORY_SEPARATOR . 'incubator' . DIRECTORY_SEPARATOR . 'library'
        . PATH_SEPARATOR . $baseZfPath . DIRECTORY_SEPARATOR . 'library'
        . get_include_path()
    );
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

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
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Soap_ServerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->server = new Zend_Soap_Server();
    }

    public function tearDown()
    {
        unset($this->server);
    }

    /**
     * @todo Implement testSetOptions().
     */
    public function testSetOptions()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetOptions().
     */
    public function testGetOptions()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    public function testEncoding()
    {
        $this->assertNull($this->server->getEncoding());
        $this->server->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->server->getEncoding());

        try {
            $this->server->setEncoding(array('UTF-8'));
            $this->fail('Non-string encoding values should fail');
        } catch (Exception $e) {
            // success
        }
    }

    public function testSoapVersion()
    {
        $this->assertEquals(SOAP_1_2, $this->server->getSoapVersion());
        $this->server->setSoapVersion(SOAP_1_1);
        $this->assertEquals(SOAP_1_1, $this->server->getSoapVersion());
        try {
            $this->server->setSoapVersion('bogus');
            $this->fail('Invalid soap versions should fail');
        } catch (Exception $e)  {
            // success
        }
    }

    public function testValidateUrn()
    {
        try {
            $this->server->validateUrn('bogosity');
            $this->fail('URNs without schemes should fail');
        } catch (Exception $e) {
            // success
        }

        $this->assertTrue($this->server->validateUrn('http://framework.zend.com/'));
        $this->assertTrue($this->server->validateUrn('urn:soapHandler/GetOpt'));
    }

    /**
     * @todo Implement testSetActor().
     */
    public function testSetActor()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetActor().
     */
    public function testGetActor()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetUri().
     */
    public function testSetUri()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetUri().
     */
    public function testGetUri()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetClassmap().
     */
    public function testSetClassmap()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetClassmap().
     */
    public function testGetClassmap()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetWsdl().
     */
    public function testSetWsdl()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetWsdl().
     */
    public function testGetWsdl()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testAddFunction().
     */
    public function testAddFunction()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetClass().
     */
    public function testSetClass()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetFunctions().
     */
    public function testGetFunctions()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testLoadFunctions().
     */
    public function testLoadFunctions()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetPersistence().
     */
    public function testSetPersistence()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetRequest().
     */
    public function testSetRequest()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetLastRequest().
     */
    public function testGetLastRequest()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetReturnResponse().
     */
    public function testSetReturnResponse()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetReturnResponse().
     */
    public function testGetReturnResponse()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetLastResponse().
     */
    public function testGetLastResponse()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testHandle().
     */
    public function testHandle()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testRegisterFaultException().
     */
    public function testRegisterFaultException()
    {
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
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}

// Call Zend_Soap_ServerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Soap_ServerTest::main") {
    Zend_Soap_ServerTest::main();
}
