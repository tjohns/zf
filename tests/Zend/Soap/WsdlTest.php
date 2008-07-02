<?php
/**
 * @package Zend_Soap
 * @subpackage UnitTests
 */

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Soap_Wsdl */
require_once 'Zend/Soap/Wsdl.php';


/**
 * Test cases for Zend_Soap_Wsdl
 *
 * @package Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_WsdlTest extends PHPUnit_Framework_TestCase
{
    function testConstructor()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                                 . 'xmlns:tns="http://localhost/MyService.php" '
                                 . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                                 . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                                 . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                                 . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);
    }
    
    function testAddMessage()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $messageParts = array();
        $messageParts['parameter1'] = $wsdl->getType('int');
        $messageParts['parameter2'] = $wsdl->getType('string');
        $messageParts['parameter3'] = $wsdl->getType('mixed');
        
        $wsdl->addMessage('myMessage', $messageParts);
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<message name="myMessage">'
                               .   '<part name="parameter1" type="xsd:int"/>'
                               .   '<part name="parameter2" type="xsd:string"/>'
                               .   '<part name="parameter3" type="xsd:anyType"/>'
                               . '</message>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddPortType()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl->addPortType('myPortType');
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddPortOperation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $portType = $wsdl->addPortType('myPortType');
        
        $wsdl->addPortOperation($portType, 'operation1');
        $wsdl->addPortOperation($portType, 'operation2', 'tns:operation2Request', 'tns:operation2Response');
        $wsdl->addPortOperation($portType, 'operation3', 'tns:operation3Request', 'tns:operation3Response', 'tns:operation3Fault');
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType">'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input message="tns:operation2Request"/>'
                               .     '<output message="tns:operation2Response"/>'
                               .   '</operation>'
                               .   '<operation name="operation3">'
                               .     '<input message="tns:operation3Request"/>'
                               .     '<output message="tns:operation3Response"/>'
                               .     '<fault message="tns:operation3Fault"/>'
                               .   '</operation>'
                               . '</portType>'
                          . '</definitions>' . PHP_EOL);
    }
    
    function testAddBinding()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl->addPortType('myPortType');
        $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType"/>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddBindingOperation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');
        
        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding, 
                                   'operation2', 
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );
        $wsdl->addBindingOperation($binding, 
                                   'operation3', 
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                   );
                                          
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               .   '<operation name="operation3">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .     '<fault>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</fault>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);
    }
    
    function testAddSoapBinding()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');
        
        $wsdl->addSoapBinding($binding);
        
        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding, 
                                   'operation2', 
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );
                                  
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);
                          
        $wsdl1 = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl1->addPortType('myPortType');
        $binding = $wsdl1->addBinding('MyServiceBinding', 'myPortType');
        
        $wsdl1->addSoapBinding($binding, 'rpc');
        
        $wsdl1->addBindingOperation($binding, 'operation1');
        $wsdl1->addBindingOperation($binding, 
                                   'operation2', 
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );
                                  
        $this->assertEquals($wsdl1->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);
    }
    
    
    function testAddSoapOperation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addSoapOperation($binding, 'http://localhost/MyService.php#myOperation');
        
        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding, 
                                   'operation2', 
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:operation soapAction="http://localhost/MyService.php#myOperation"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);
    }
    
    function testAddService()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl->addPortType('myPortType');
        $wsdl->addBinding('MyServiceBinding', 'myPortType');
        
        $wsdl->addService('Service1', 'myPortType', 'MyServiceBinding', 'http://localhost/MyService.php');
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType"/>'
                               . '<service name="Service1">'
                               .   '<port name="myPortType" binding="MyServiceBinding">'
                               .     '<soap:address location="http://localhost/MyService.php"/>'
                               .   '</port>'
                               . '</service>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddDocumentation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $portType = $wsdl->addPortType('myPortType');
        
        $wsdl->addDocumentation($portType, 'This is a description for Port Type node.');
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType">'
                               .   '<documentation>This is a description for Port Type node.</documentation>'
                               . '</portType>'
                          . '</definitions>' . PHP_EOL);
    }

    function testToXml()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);
    }

    function testToDomDocument()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        $dom = $wsdl->toDomDocument();
        
        $this->assertTrue($dom instanceOf DOMDocument);
        
        $this->assertEquals($dom->saveXML(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);
    }
    
    function testDump()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        ob_start();
        $wsdl->dump();
        $wsdlDump = ob_get_contents();
        ob_end_clean();
        
        $this->assertEquals($wsdlDump, 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);

        $wsdl->dump(dirname(__FILE__) . '/_files/dumped.wsdl');
        $dumpedContent = file_get_contents(dirname(__FILE__) . '/_files/dumped.wsdl');
        
        $this->assertEquals($dumpedContent, 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);
        
        unlink(dirname(__FILE__) . '/_files/dumped.wsdl');
    }
    
    function testGetType()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals($wsdl->getType('string'),  'xsd:string');
        $this->assertEquals($wsdl->getType('str'),     'xsd:string');
        $this->assertEquals($wsdl->getType('int'),     'xsd:int');
        $this->assertEquals($wsdl->getType('integer'), 'xsd:int');
        $this->assertEquals($wsdl->getType('float'),   'xsd:float');
        $this->assertEquals($wsdl->getType('double'),  'xsd:float');
        $this->assertEquals($wsdl->getType('boolean'), 'xsd:boolean');
        $this->assertEquals($wsdl->getType('bool'),    'xsd:boolean');
        $this->assertEquals($wsdl->getType('array'),   'soap-enc:Array');
        $this->assertEquals($wsdl->getType('object'),  'xsd:struct');
        $this->assertEquals($wsdl->getType('mixed'),   'xsd:anyType');
        $this->assertEquals($wsdl->getType('void'),    '');

        $this->assertEquals($wsdl->getType('Zend_Soap_Wsdl_Test'),
                            'tns:Zend_Soap_Wsdl_Test');
        
        $wsdl1 = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', false);
        
        $this->assertEquals($wsdl1->getType('Zend_Soap_Wsdl_Test'), 'xsd:anyType');
        
    }
    
    function testAddComplexType()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        
        $wsdl->addComplexType('Zend_Soap_Wsdl_Test');
        
        $this->assertEquals($wsdl->toXml(), 
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<types>'
                               .   '<xsd:schema targetNamespace="">'
                               .     '<xsd:complexType name="Zend_Soap_Wsdl_Test">'
                               .       '<xsd:all>'
                               .         '<xsd:element name="var1" type="xsd:int"/>'
                               .         '<xsd:element name="var2" type="xsd:string"/>'
                               .       '</xsd:all>'
                               .     '</xsd:complexType>'
                               .   '</xsd:schema>'
                               . '</types>'
                          . '</definitions>' . PHP_EOL);
    }
}



/**
 * Test Class
 */
class Zend_Soap_Wsdl_Test {
    /**
     * @var integer
     */
    public $var1;
    
    /**
     * @var string
     */
    public $var2;
}

