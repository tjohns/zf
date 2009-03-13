<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";
require_once "Zend/Soap/Wsdl.php";
require_once "Zend/Soap/Wsdl/Parser.php";

class Zend_Soap_Wsdl_ParserTest extends PHPUnit_Framework_TestCase
{
    protected function getWsdlExampleDom()
    {
        $dom = new DOMDocument();
        $dom->loadXml(file_get_contents(dirname(__FILE__)."/../_files/wsdl_example.wsdl"));
        return $dom;
    }

    public function testFactoryWithDomDocument()
    {
        $dom = $this->getWsdlExampleDom();
        $parser = Zend_Soap_Wsdl_Parser::import($dom);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithString()
    {
        $xmlString = file_get_contents(dirname(__FILE__)."/../_files/wsdl_example.wsdl");
        $parser = Zend_Soap_Wsdl_Parser::import($xmlString);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithSimpleXml()
    {
        $xmlString = file_get_contents(dirname(__FILE__)."/../_files/wsdl_example.wsdl");
        $simpleXml = simplexml_load_string($xmlString);
        $parser = Zend_Soap_Wsdl_Parser::import($simpleXml);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithZendSoapWsdl()
    {
        $wsdl = new Zend_Soap_Wsdl("name", "http://example.com");
        $parser = Zend_Soap_Wsdl_Parser::import($wsdl);
        $this->assertTrue($parser instanceof Zend_Soap_Wsdl_Parser);
    }

    public function testFactoryWithInvalidData()
    {
        try {
            $parser = Zend_Soap_Wsdl_Parser::import(null);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    public function testParserApiHasFluentSetWsdl()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWsdlExampleDom();
        $parser = Zend_Soap_Wsdl_Parser::import($dom);

        // SetWsdl is a fluent function
        $this->assertTrue( ($parser->setWsdl($dom->saveXML())) instanceof Zend_Soap_Wsdl_Parser );
    }

    public function testParserApiReturnDocumentOnParsing()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWsdlExampleDom();
        $parser = Zend_Soap_Wsdl_Parser::import($dom);

        // Parse returns Result
        $result = $parser->parse();
        $this->assertTrue($result instanceof Zend_Soap_Wsdl_Document);
    }

    public function testParserDocumentResultApiInterface()
    {
        $document = new Zend_Soap_Wsdl_Document(
            "name",
            Zend_Soap_Wsdl_Document::WSDL_11,
            new Zend_Soap_Wsdl_Element_Collection("Operation"),
            new Zend_Soap_Wsdl_Element_Collection("Port"),
            new Zend_Soap_Wsdl_Element_Collection("Binding"),
            new Zend_Soap_Wsdl_Element_Collection("Service"),
            new Zend_Soap_Wsdl_Element_Collection("Type"),
            "docs",
            array("ns1" => "nsuri1", "ns2" => "nsuri2")
        );

        $this->assertEquals("name",         $document->getName());
        $this->assertEquals(Zend_Soap_Wsdl_Document::WSDL_11,            $document->getVersion());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Operation",          $document->getOperations()->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Port",               $document->getPortTypes()->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Binding",            $document->getBindings()->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Service",            $document->getServices()->getType());
        $this->assertEquals("Zend_Soap_Wsdl_Element_Type",               $document->getTypes()->getType());
        $this->assertEquals("docs",                                      $document->getDocumentation());
        $this->assertEquals(array("ns1" => "nsuri1", "ns2" => "nsuri2"), $document->getNamespaces());
    }

    public function testParseExampleWsdlAndCountResultElements()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWsdlExampleDom();
        $parser = Zend_Soap_Wsdl_Parser::import($dom);

        $document = $parser->parse();

        $this->assertEquals("Zend_Soap_Server_TestClass", $document->getName());
        $this->assertEquals(Zend_Soap_Wsdl_Document::WSDL_11, $document->getVersion());
        $this->assertEquals(4, count($document->getOperations()),   "Number of operations does not match.");
        $this->assertEquals(1, count($document->getPortTypes()),    "Number of ports does not match.");
        $this->assertEquals(1, count($document->getBindings()),     "Number of bindings does not match.");
        $this->assertEquals(1, count($document->getServices()),     "Number of services does not match.");
        $this->assertEquals(2, count($document->getTypes()),        "Number of types does not match.");
    }

    public function testParseExampleWsdlAndCheckMatchingNames()
    {
        // Constructor expects DOMDocument instance
        $dom = $this->getWsdlExampleDom();
        $parser = Zend_Soap_Wsdl_Parser::import($dom);

        $document = $parser->parse();

        $this->assertEquals(array("testFunc1", "testFunc2", "testFunc3", "testFunc4"), $document->getOperations()->getElementNames());
        $this->assertEquals(array("Zend_Soap_Server_TestClassBinding"), $document->getBindings()->getElementNames());
        $this->assertEquals(array("Zend_Soap_Server_TestClassPort"),    $document->getPortTypes()->getElementNames());
        $this->assertEquals(array("Zend_Soap_Server_TestClassService"), $document->getServices()->getElementNames());
    }

    public function testParseExampleWsdlAndEvaluateDocumentationBlocks()
    {
        $dom        = $this->getWsdlExampleDom();
        $parser     = Zend_Soap_Wsdl_Parser::import($dom);
        $document   = $parser->parse();

        $this->assertEquals("Definitions", $document->getDocumentation());
    }

    public function testParseExampleWsdlAndEvaluateComplexTypeDefinitions()
    {
        $dom = $this->getWsdlExampleDom();
        $parser = Zend_Soap_Wsdl_Parser::import($dom);
        $document = $parser->parse();

        $types      = $document->getTypes();
        $typeNames  = array("ArrayOfZend_Soap_Wsdl_ComplexTest", "Zend_Soap_Wsdl_ComplexTest");
        $this->assertEquals($typeNames, $types->getElementNames());

        $complexTypeSubtypes = array(
            "var" => array("xsd:name" => "var", "xsd:type" => "xsd:int"),
            "var2" => array("xsd:name" => "var2", "xsd:type" => "xsd:string"),
        );
        $this->assertEquals($complexTypeSubtypes, $types->getElement("Zend_Soap_Wsdl_ComplexTest")->getSubTypes());
    }
}