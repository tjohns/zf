<?php

class Zend_Entity_Mapper_Definition_PropertyTest extends PHPUnit_Framework_TestCase
{
    public function testSetPropertyName()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $this->assertEquals("name1", $property->getPropertyName());
        $property->setPropertyName("name2");
        $this->assertEquals("name2", $property->getPropertyName());
    }

    public function testSetGetColumnName()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $property->setColumnName("name2");
        $this->assertEquals("name2", $property->getColumnName());
    }

    public function testSetColumnNameConstructor()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1", array("columnName" => "name2"));
        $this->assertEquals("name2", $property->getColumnName());
    }

    public function testSetGetPropertyType()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $property->setPropertyType("asfd");
        $this->assertEquals("asfd", $property->getPropertyType());
    }

    public function testSetPropertyTypeConstructor()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1", array("propertyType" => "asdf"));
        $this->assertEquals("asdf", $property->getPropertyType());
    }
}