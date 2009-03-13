<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";
require_once "Zend/Soap/Wsdl/Element/Binding.php";
require_once "Zend/Soap/Wsdl/Element/Type.php";
require_once "Zend/Soap/Wsdl/Element/Message.php";
require_once "Zend/Soap/Wsdl/Element/Operation.php";
require_once "Zend/Soap/Wsdl/Element/Port.php";
require_once "Zend/Soap/Wsdl/Element/Service.php";
require_once "Zend/Soap/Wsdl/Element/Collection.php";

class Zend_Soap_Wsdl_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testBindingElementApiGetter()
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("test");
        $binding = new Zend_Soap_Wsdl_Element_Binding("name1", "port1", $operations, "test");

        $this->assertEquals("name1",     $binding->getName());
        $this->assertEquals("port1",     $binding->getPortName());
        $this->assertEquals($operations, $binding->getOperations());
        $this->assertEquals("test",      $binding->getDocumentation());
    }

    public function testBindingElementApiThrowsExceptionUponInvalidInput()
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("test");
        
        try {
            $binding = new Zend_Soap_Wsdl_Element_Binding(array(), "portName", $operations, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    public function testTypeElementApiGetter()
    {
        $type = new Zend_Soap_Wsdl_Element_Type("name1", "xsd:all", array("test"), "test");

        $this->assertEquals("name1", $type->getName());
        $this->assertEquals("xsd:all", $type->getSubTypeSpec());
        $this->assertEquals(array("test"), $type->getSubTypes());
        $this->assertEquals("test", $type->getDocumentation());
    }

    public function testTypeElementApiThrowsExceptionUponInvalidInput()
    {
        try {
            $type = new Zend_Soap_Wsdl_Element_Type(array(), "blub", array(), "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testMessageElementApiGetter()
    {
        $parts = new Zend_Soap_Wsdl_Element_Collection("test");
        $message = new Zend_Soap_Wsdl_Element_Message("name1", $parts, "test");

        $this->assertEquals("name1", $message->getName());
        $this->assertEquals($parts, $message->getParts());
        $this->assertEquals("test", $message->getDocumentation());
    }

    public function testMessageElementApiThrowsExceptionUponInvalidInput()
    {
        $parts = new Zend_Soap_Wsdl_Element_Collection("test");
        try {
            $message = new Zend_Soap_Wsdl_Element_Message(array(), $parts, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testMessagePartElementApiGetter()
    {
        $part = new Zend_Soap_Wsdl_Element_Part("name1", "type2", "documentation3");

        $this->assertEquals("name1", $part->getName());
        $this->assertEquals("type2", $part->getType());
        $this->assertEquals("documentation3", $part->getDocumentation());
    }

    public function testPortElementApiGetter()
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("test");
        $port = new Zend_Soap_Wsdl_Element_Port("name1", $operations, "test");

        $this->assertEquals("name1", $port->getName());
        $this->assertEquals($operations, $port->getOperations());
        $this->assertEquals("test", $port->getDocumentation());
    }

    public function testPortElementApiThrowsExceptionUponInvalidInput()
    {
        $operations = new Zend_Soap_Wsdl_Element_Collection("test");
        try {
            $port = new Zend_Soap_Wsdl_Element_Port(array(), $operations, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testOperationElementApiGetter()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("test");
        $input = new Zend_Soap_Wsdl_Element_Message("name", $collection, "test");
        $output = new Zend_Soap_Wsdl_Element_Message("name", $collection, "test");
        
        $operation = new Zend_Soap_Wsdl_Element_Operation("name1", "test", $input, $output);

        $this->assertEquals("name1",    $operation->getName());
        $this->assertEquals($input,     $operation->getInputMessage());
        $this->assertEquals($output,    $operation->getOutputMessage());
        $this->assertEquals("test",     $operation->getDocumentation());
    }

    public function testOperationElementApiThrowsExceptionUponInvalidInput()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("test");
        $input = new Zend_Soap_Wsdl_Element_Message("name", $collection, "test");
        $output = new Zend_Soap_Wsdl_Element_Message("name", $collection, "test");
        
        try {
            $operation = new Zend_Soap_Wsdl_Element_Operation(array(), "test", $input, $output);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testServiceElementApiGetter()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("test");
        $port = new Zend_Soap_Wsdl_Element_Port("name", $collection, "test");
        $binding = new Zend_Soap_Wsdl_Element_Binding("name", "port", $collection, "test");

        $service = new Zend_Soap_Wsdl_Element_Service("service", "address", $port, $binding, "test");

        $this->assertEquals("service",  $service->getName());
        $this->assertEquals("address",  $service->getSoapAddress());
        $this->assertEquals($port,      $service->getPort());
        $this->assertEquals($binding,   $service->getBinding());
        $this->assertEquals("test",     $service->getDocumentation());
    }

    public function testServiceElementApiThrowsExceptionUponInvalidServiceNameInput()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("test");
        $port = new Zend_Soap_Wsdl_Element_Port("name", $collection, "test");
        $binding = new Zend_Soap_Wsdl_Element_Binding("name", "port", $collection, "test");

        try {
            $service = new Zend_Soap_Wsdl_Element_Service(array(), "address", $port, $binding, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    public function testServiceElementApiThrowsExceptionUponInvalidSoapAddressInput()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("test");
        $port = new Zend_Soap_Wsdl_Element_Port("name", $collection, "test");
        $binding = new Zend_Soap_Wsdl_Element_Binding("name", "port", $collection, "test");
        
        try {
            $service = new Zend_Soap_Wsdl_Element_Service("name", array(), $port, $binding, "test");
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testCollectionElementApiIsIterator()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("Operation");
        $this->assertTrue($collection instanceof Iterator);
    }

    public function testCollectionElementApiIsCountable()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("Operation");
        $this->assertTrue($collection instanceof Countable);
    }

    public function testCollectionAddNonMatchingTypeThrowsException()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("Operation");
        try {
            $type = new Zend_Soap_Wsdl_Element_Type("type", "xsd:anyType", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");
            $collection->addElement($type);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testConstructorOfCollectionExpectsTypeStringAsArgument()
    {
        try {
            $collection = new Zend_Soap_Wsdl_Element_Collection(false);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testCollectionElementApiType()
    {
        $collection = new Zend_Soap_Wsdl_Element_Collection("Operation");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Operation", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Type");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Type", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Binding");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Binding", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Service");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Service", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Port");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Port", $collection->getType());

        $collection = new Zend_Soap_Wsdl_Element_Collection("Message");
        $this->assertEquals("Zend_Soap_Wsdl_Element_Message", $collection->getType());
    }

    public function testCollectionElementAddMultipleApi()
    {
        $collection         = new Zend_Soap_Wsdl_Element_Collection("Message");
        $message1           = new Zend_Soap_Wsdl_Element_Message("message1", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");
        $message2           = new Zend_Soap_Wsdl_Element_Message("message2", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");

        $collection->addElement($message1);
        $collection->addElement($message2);
        $this->assertEquals(array("message1", "message2"), $collection->getElementNames());
        $this->assertEquals($message2, $collection->getElement("message2"));
        $this->assertEquals(2, count($collection));
    }

    public function testCollectionElementAddDuplicateElementThrowsException()
    {
        $collection         = new Zend_Soap_Wsdl_Element_Collection("Message");
        $message2           = new Zend_Soap_Wsdl_Element_Message("message2", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");
        $messageDuplicate   = new Zend_Soap_Wsdl_Element_Message("message2", new Zend_Soap_Wsdl_Element_Collection("Type"), "test");

        $collection->addElement($message2);
        try {
            // Adding duplicate message leads to exception
            $collection->addElement($messageDuplicate);
            $this->fail("Adding a duplicate named element to a collection should throw an exception.");
        } catch(Zend_Soap_Wsdl_Exception $e) {
            $this->assertEquals(1, count($collection));
        }
    }

    public function testCollectionAccessingUnknownElementThrowsException()
    {
        $collection         = new Zend_Soap_Wsdl_Element_Collection("Message");
        try {
            // Accessing unkown message leads to exception
            $collection->getElement("messageUnknown");
            $this->fail("Accessing unknown element should throw an exception.");
        }  catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }
}