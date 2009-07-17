<?php

class Zend_Entity_StateTransformer_ArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Entity_StateTransformer_Array
     */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new Zend_Entity_StateTransformer_Array();
    }

    public function testNoGetState()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception",
            "Array Transformer requires getState() method on entity 'stdClass'."
        );
        $this->transformer->getState(new stdClass);
    }

    public function testGetStatePropertyNotExists()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception"
        );

        $this->transformer->setPropertyNames(array("foo"));

        $entityMock = $this->getMock('Zend_Entity_Interface');
        $entityMock->expects($this->once())
                   ->method('getState')
                   ->will($this->returnValue(array()));

        $this->transformer->getState($entityMock);
    }

    public function testGetStateFromObject()
    {
        $this->transformer->setPropertyNames(array("foo"));

        $entityMock = $this->getMock('Zend_Entity_Interface');
        $entityMock->expects($this->once())
                   ->method('getState')
                   ->will($this->returnValue(array("foo" => 1)));

        $entityState = $this->transformer->getState($entityMock);

        $this->assertEquals(
            array("foo" => 1),
            $entityState
        );
    }

    public function testGetStateOnlyReturnsRegisteredProperties()
    {
        $this->transformer->setPropertyNames(array("foo"));

        $entityMock = $this->getMock('Zend_Entity_Interface');
        $entityMock->expects($this->once())
                   ->method('getState')
                   ->will($this->returnValue(array("foo" => 1, "bar" => 2)));

        $entityState = $this->transformer->getState($entityMock);

        $this->assertEquals(
            array("foo" => 1),
            $entityState
        );
    }

    public function testSetStateWithoutMethodThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception"
        );

        $this->transformer->setState(new stdClass(), array());
    }

    public function testSetIdWithoutSetMethodThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception"
        );

        $this->transformer->setId(new stdClass(), "id", "7");
    }
}
