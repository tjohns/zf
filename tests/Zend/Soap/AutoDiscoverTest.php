<?php
/**
 * @package Zend_Soap
 * @subpackage UnitTests
 */

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Soap_AutoDiscover */
require_once 'Zend/Soap/AutoDiscover.php';


/**
 * Test cases for Zend_Soap_AutoDiscover
 *
 * @package Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{
    function testSetClass()
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = 'localhost';
        }
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            $_SERVER['SCRIPT_NAME'] = '/my_script.php';
        }
        $scriptUri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];


        $server = new Zend_Soap_AutoDiscover();
        $server->setClass('Zend_Soap_AutoDiscover_Test');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_clean());
        $wsdl = '<?xml version="1.0"?>' . PHP_EOL
              . '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
              .              'xmlns:tns="' . $scriptUri . '" '
              .              'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
              .              'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
              .              'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
              .              'name="Zend_Soap_AutoDiscover_Test" '
              .              'targetNamespace="' . $scriptUri . '">'
              .     '<portType name="Zend_Soap_AutoDiscover_TestPort">'
              .         '<operation name="testFunc1">'
              .             '<input message="tns:testFunc1Request"/>'
              .             '<output message="tns:testFunc1Response"/>'
              .         '</operation>'
              .         '<operation name="testFunc2">'
              .             '<input message="tns:testFunc2Request"/>'
              .             '<output message="tns:testFunc2Response"/>'
              .         '</operation>'
              .         '<operation name="testFunc3">'
              .             '<input message="tns:testFunc3Request"/>'
              .             '<output message="tns:testFunc3Response"/>'
              .         '</operation><operation name="testFunc4">'
              .             '<input message="tns:testFunc4Request"/>'
              .             '<output message="tns:testFunc4Response"/>'
              .         '</operation>'
              .     '</portType>'
              .     '<binding name="Zend_Soap_AutoDiscover_TestBinding" type="tns:Zend_Soap_AutoDiscover_TestPort">'
              .         '<soap:operation soapAction="' . $scriptUri . '#testFunc4"/>'
              .         '<soap:operation soapAction="' . $scriptUri . '#testFunc3"/>'
              .         '<soap:operation soapAction="' . $scriptUri . '#testFunc2"/>'
              .         '<soap:operation soapAction="' . $scriptUri . '#testFunc1"/>'
              .         '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'
              .         '<operation name="testFunc1">'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc2">'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc3">'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>'
              .         '</operation>'
              .         '<operation name="testFunc4">'
              .             '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>'
              .             '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>'
              .         '</operation>'
              .     '</binding>'
              .     '<service name="Zend_Soap_AutoDiscover_TestService">'
              .         '<port name="Zend_Soap_AutoDiscover_TestPort" binding="tns:Zend_Soap_AutoDiscover_TestBinding">'
              .             '<soap:address location="' . $scriptUri . '"/>'
              .         '</port>'
              .     '</service>'
              .     '<message name="testFunc1Request"/>'
              .     '<message name="testFunc1Response"><part name="testFunc1Return" type="xsd:string"/></message>'
              .     '<message name="testFunc2Request"><part name="who" type="xsd:string"/></message>'
              .     '<message name="testFunc2Response"><part name="testFunc2Return" type="xsd:string"/></message>'
              .     '<message name="testFunc3Request"><part name="who" type="xsd:string"/><part name="when" type="xsd:int"/></message>'
              .     '<message name="testFunc3Response"><part name="testFunc3Return" type="xsd:string"/></message>'
              .     '<message name="testFunc4Request"/>'
              .     '<message name="testFunc4Response"><part name="testFunc4Return" type="xsd:string"/></message>'
              . '</definitions>' . PHP_EOL;

        $dom->save(dirname(__FILE__).'/_files/setclass.wsdl');
        $this->assertEquals($wsdl, $dom->saveXML());
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/setclass.wsdl');
    }

    function testAddFunctionSimple()
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = 'localhost';
        }
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            $_SERVER['SCRIPT_NAME'] = '/my_script.php';
        }
        $scriptUri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_contents());
        $dom->save(dirname(__FILE__).'/_files/addfunction.wsdl');

        ob_end_clean();
        $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
        $name = $parts[0];

        $wsdl = '<?xml version="1.0"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" name="' .$name. '" targetNamespace="' . $scriptUri . '"><portType name="' .$name. 'Port"><operation name="Zend_Soap_AutoDiscover_TestFunc"><input message="tns:Zend_Soap_AutoDiscover_TestFuncRequest"/><output message="tns:Zend_Soap_AutoDiscover_TestFuncResponse"/></operation></portType><binding name="' .$name. 'Binding" type="tns:' .$name. 'Port"><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc"/><soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/><operation name="Zend_Soap_AutoDiscover_TestFunc"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation></binding><service name="' .$name. 'Service"><port name="' .$name. 'Port" binding="tns:' .$name. 'Binding"><soap:address location="' . $scriptUri . '"/></port></service><message name="Zend_Soap_AutoDiscover_TestFuncRequest"><part name="who" type="xsd:string"/></message><message name="Zend_Soap_AutoDiscover_TestFuncResponse"><part name="Zend_Soap_AutoDiscover_TestFuncReturn" type="xsd:string"/></message></definitions>
';
        $this->assertEquals($wsdl, $dom->saveXML(), "Bad WSDL generated");
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/addfunction.wsdl');
    }

    function testAddFunctionMultiple()
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = 'localhost';
        }
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            $_SERVER['SCRIPT_NAME'] = '/my_script.php';
        }
        $scriptUri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

        $server = new Zend_Soap_AutoDiscover();
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc2');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc3');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc4');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc5');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc6');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc7');
        $server->addFunction('Zend_Soap_AutoDiscover_TestFunc9');

        $dom = new DOMDocument();
        ob_start();
        $server->handle();
        $dom->loadXML(ob_get_contents());
        $dom->save(dirname(__FILE__).'/_files/addfunction2.wsdl');

        ob_end_clean();

        $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
        $name = $parts[0];

        $wsdl = '<?xml version="1.0"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="' . $scriptUri . '" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" name="' .$name. '" targetNamespace="' . $scriptUri . '"><portType name="' .$name. 'Port"><operation name="Zend_Soap_AutoDiscover_TestFunc"><input message="tns:Zend_Soap_AutoDiscover_TestFuncRequest"/><output message="tns:Zend_Soap_AutoDiscover_TestFuncResponse"/></operation><operation name="Zend_Soap_AutoDiscover_TestFunc2"><input message="tns:Zend_Soap_AutoDiscover_TestFunc2Request"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc2Response"/></operation><operation name="Zend_Soap_AutoDiscover_TestFunc3"><input message="tns:Zend_Soap_AutoDiscover_TestFunc3Request"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc3Response"/></operation><operation name="Zend_Soap_AutoDiscover_TestFunc4"><input message="tns:Zend_Soap_AutoDiscover_TestFunc4Request"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc4Response"/></operation><operation name="Zend_Soap_AutoDiscover_TestFunc5"><input message="tns:Zend_Soap_AutoDiscover_TestFunc5Request"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc5Response"/></operation><operation name="Zend_Soap_AutoDiscover_TestFunc6"><input message="tns:Zend_Soap_AutoDiscover_TestFunc6Request"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc6Response"/></operation><operation name="Zend_Soap_AutoDiscover_TestFunc7"><input message="tns:Zend_Soap_AutoDiscover_TestFunc7Request"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc7Response"/></operation><operation name="Zend_Soap_AutoDiscover_TestFunc9"><input message="tns:Zend_Soap_AutoDiscover_TestFunc9Request"/><output message="tns:Zend_Soap_AutoDiscover_TestFunc9Response"/></operation></portType><binding name="' .$name. 'Binding" type="tns:' .$name. 'Port"><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc9"/><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc7"/><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc6"/><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc5"/><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc4"/><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc3"/><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc2"/><soap:operation soapAction="' . $scriptUri . '#Zend_Soap_AutoDiscover_TestFunc"/><soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/><operation name="Zend_Soap_AutoDiscover_TestFunc"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="Zend_Soap_AutoDiscover_TestFunc2"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="Zend_Soap_AutoDiscover_TestFunc3"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="Zend_Soap_AutoDiscover_TestFunc4"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="Zend_Soap_AutoDiscover_TestFunc5"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="Zend_Soap_AutoDiscover_TestFunc6"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="Zend_Soap_AutoDiscover_TestFunc7"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation><operation name="Zend_Soap_AutoDiscover_TestFunc9"><input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input><output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output></operation></binding><service name="' .$name. 'Service"><port name="' .$name. 'Port" binding="tns:' .$name. 'Binding"><soap:address location="' . $scriptUri . '"/></port></service><message name="Zend_Soap_AutoDiscover_TestFuncRequest"><part name="who" type="xsd:string"/></message><message name="Zend_Soap_AutoDiscover_TestFuncResponse"><part name="Zend_Soap_AutoDiscover_TestFuncReturn" type="xsd:string"/></message><message name="Zend_Soap_AutoDiscover_TestFunc2Request"/><message name="Zend_Soap_AutoDiscover_TestFunc3Request"/><message name="Zend_Soap_AutoDiscover_TestFunc3Response"><part name="Zend_Soap_AutoDiscover_TestFunc3Return" type="xsd:boolean"/></message><message name="Zend_Soap_AutoDiscover_TestFunc4Request"/><message name="Zend_Soap_AutoDiscover_TestFunc4Response"><part name="Zend_Soap_AutoDiscover_TestFunc4Return" type="xsd:boolean"/></message><message name="Zend_Soap_AutoDiscover_TestFunc5Request"/><message name="Zend_Soap_AutoDiscover_TestFunc5Response"><part name="Zend_Soap_AutoDiscover_TestFunc5Return" type="xsd:int"/></message><message name="Zend_Soap_AutoDiscover_TestFunc6Request"/><message name="Zend_Soap_AutoDiscover_TestFunc6Response"><part name="Zend_Soap_AutoDiscover_TestFunc6Return" type="xsd:string"/></message><message name="Zend_Soap_AutoDiscover_TestFunc7Request"/><message name="Zend_Soap_AutoDiscover_TestFunc7Response"><part name="Zend_Soap_AutoDiscover_TestFunc7Return" type="soap-enc:Array"/></message><message name="Zend_Soap_AutoDiscover_TestFunc9Request"><part name="foo" type="xsd:string"/><part name="bar" type="xsd:string"/></message><message name="Zend_Soap_AutoDiscover_TestFunc9Response"><part name="Zend_Soap_AutoDiscover_TestFunc9Return" type="xsd:string"/></message></definitions>
';
        $this->assertEquals($wsdl, $dom->saveXML(), "Bad WSDL generated");
        $this->assertTrue($dom->schemaValidate(dirname(__FILE__) .'/schemas/wsdl.xsd'), "WSDL Did not validate");

        unlink(dirname(__FILE__).'/_files/addfunction2.wsdl');
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
 * Multiple Args
 *
 * @param string $foo
 * @param string $bar
 * @return string
 */
function Zend_Soap_AutoDiscover_TestFunc9($foo, $bar)
{
    return "$foo $bar";
}

/**
 * Test Class
 */
class Zend_Soap_AutoDiscover_Test {
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
