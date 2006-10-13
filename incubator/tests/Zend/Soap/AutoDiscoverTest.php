<?php
/**
 * @package Zend_Rest
 * @subpackage UnitTests
 */

/**
 * Zend_Soap_AutoDiscover
 */
require_once 'Zend/Soap/AutoDiscover.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test cases for Zend_Soap_AutoDiscover
 *
 * @package Zend_Rest
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase 
{
	function testSetClass()
	{
		$server = new Zend_Soap_AutoDiscover();
		$server->setClass('Zend_Soap_AutoDiscover_Test');
		$dom = new DOMDocument();
		ob_start();
		$server->handle();
		$dom->loadXML(ob_get_clean());
		$wsdl = '<?xml version="1.0"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="http://dummy.php" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" name="Zend_Soap_AutoDiscover_Test" targetNamespace="http://dummy.php"><portType name="Zend_Soap_AutoDiscover_TestPortType"><operation name="testFunc2"><input message="tns:testFunc2Request"/><output message="tns:testFunc2Response"/></operation><operation name="testFunc3"><input message="tns:testFunc3Request"/><output message="tns:testFunc3Response"/></operation></portType><binding name="Zend_Soap_AutoDiscover_TestBinding" type="tns:Zend_Soap_AutoDiscover_TestPortType"><soap:operation soapAction="http://dummy.php#testFunc3"/><soap:operation soapAction="http://dummy.php#testFunc2"/><soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/><operation name="testFunc2"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="testFunc3"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation></binding><service name="Zend_Soap_AutoDiscover_TestService"><port name="Zend_Soap_AutoDiscover_TestPort" binding="tns:Zend_Soap_AutoDiscover_TestBinding"><soap:address location="http://dummy.php"/></port></service><message name="testFunc2Request"><part name="who" type="xsd:string"/></message><message name="testFunc3Request"><part name="who" type="xsd:string"/><part name="when" type="xsd:int"/></message><message name="testFunc3Response"><part name="testFunc3Return" type="xsd:string"/></message></definitions>
';
		$dom->save(dirname(__FILE__).'/setclass.wsdl');
		$this->assertEquals($wsdl, $dom->saveXML());
		$this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");
	}
	
	function testAddFunctionSimple()
	{
		$server = new Zend_Soap_AutoDiscover();
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
		$dom = new DOMDocument();
		ob_start();
		$server->handle();
		$dom->loadXML(ob_get_contents());
		$dom->save(dirname(__FILE__).'/addfunction.wsdl');

		ob_end_clean();
		$wsdl = '<?xml version="1.0"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="http://dummy.php" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" name="dummy" targetNamespace="http://dummy.php"><portType name="dummyPortType"><operation name="Zend_Soap_AutoDiscover_TestFunc"><input message="tns:Zend_Soap_AutoDiscover_TestFuncRequest"/><output message="tns:Zend_Soap_AutoDiscover_TestFuncResponse"/></operation></portType><binding name="dummyBinding" type="tns:dummyPortType"><soap:operation soapAction="http://dummy.php#Zend_Soap_AutoDiscover_TestFunc"/><soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/><operation name="Zend_Soap_AutoDiscover_TestFunc"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation></binding><service name="dummyService"><port name="dummyPort" binding="tns:dummyBinding"><soap:address location="http://dummy.php"/></port></service><message name="Zend_Soap_AutoDiscover_TestFuncRequest"><part name="who" type="xsd:string"/></message><message name="Zend_Soap_AutoDiscover_TestFuncResponse"><part name="Zend_Soap_AutoDiscover_TestFuncReturn" type="xsd:string"/></message></definitions>
';
		$this->assertEquals($wsdl, $dom->saveXML(), "Bad WSDL generated");
		$this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");
	}
	
	function testAddFunctionMultiple()
	{
		$server = new Zend_Soap_AutoDiscover();
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc2');
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc3');
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc4');
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc5');
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc6');
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc7');
		$server->addFunction('Zend_Soap_AutoDiscover_TestFunc8');
		
		$dom = new DOMDocument();
		ob_start();
		$server->handle();
		$dom->loadXML(ob_get_contents());
		$dom->save(dirname(__FILE__).'/addfunction.wsdl');

		ob_end_clean();
		$wsdl = '<?xml version="1.0"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="http://dummy.php" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" name="dummy" targetNamespace="http://dummy.php"><portType name="dummyPortType"><operation name="Zend_Soap_AutoDiscover_TestFunc"><input message="tns:Zend_Soap_AutoDiscover_TestFuncRequest"/><output message="tns:Zend_Soap_AutoDiscover_TestFuncResponse"/></operation></portType><binding name="dummyBinding" type="tns:dummyPortType"><soap:operation soapAction="http://dummy.php#Zend_Soap_AutoDiscover_TestFunc"/><soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/><operation name="Zend_Soap_AutoDiscover_TestFunc"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation></binding><service name="dummyService"><port name="dummyPort" binding="tns:dummyBinding"><soap:address location="http://dummy.php"/></port></service><message name="Zend_Soap_AutoDiscover_TestFuncRequest"><part name="who" type="xsd:string"/></message><message name="Zend_Soap_AutoDiscover_TestFuncResponse"><part name="Zend_Soap_AutoDiscover_TestFuncReturn" type="xsd:string"/></message></definitions>
';
		//$this->assertEquals($wsdl, $dom->saveXML(), "Bad WSDL generated");
		$this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");
	}
}

/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function Zend_Soap_AutoDiscover_TestFunc($who) 
{
	return "Hello $who";
}

/**
 * Test Function 2
 */
function Zend_Soap_AutoDiscover_TestFunc2()
{
	return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function Zend_Soap_AutoDiscover_TestFunc3()
{
	return false;
}

/**
 * Return true
 *
 * @return bool
 */
function Zend_Soap_AutoDiscover_TestFunc4()
{
	return true;
}

/**
 * Return integer
 *
 * @return int
 */
function Zend_Soap_AutoDiscover_TestFunc5()
{
	return 123;
}

/**
 * Return string
 *
 * @return string
 */
function Zend_Soap_AutoDiscover_TestFunc6()
{
	return "string";
}

/**
 * Return array
 *
 * @return array
 */
function Zend_Soap_AutoDiscover_TestFunc7()
{
	return array('foo' => 'bar', 'baz' => true, 1 => false, 'bat' => 123);
}

/**
 * Return Object
 *
 * @return StdClass
 */
function Zend_Soap_AutoDiscover_TestFunc8()
{
	$return = (object) array('foo' => 'bar', 'baz' => true, 'bat' => 123, 'qux' => false);
	return $return;
}

/**
 * Test Class
 */
class Zend_Soap_AutoDiscover_Test {
	/**
	 * Test Function
	 */
	function testFunc()
	{
		return "Hello World";
	}
	
	/**
	 * Test Function 2
	 * 
	 * @param string $who Some Arg
	 */
	function testFunc2($who)
	{
		return "Hello $who";
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
	
	static function testFunc4()
	{
		return "I'm Static!";
	}
}