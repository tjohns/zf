<?php

class Zend_Entity_Mapper_Definition_PropertyTest extends Zend_Entity_Mapper_Definition_TestCase
{
    public function testSetPropertyNameViaConstructor()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $this->assertEquals("name1", $property->getPropertyName());
    }

    public function testResetPropertyNameWithMethod()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
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

    public function testCompilePropertySetsNameToColumnNameIfNull()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $property->compile($this->createEntityDefinitionMock(), $this->createEntityResourceMock());

        $this->assertEquals("name1", $property->getColumnName());
    }

    public function testCompilePropertyNotSetsNameToColumnNameIfNotNull()
    {
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $property->setColumnName("name2");
        $property->compile($this->createEntityDefinitionMock(), $this->createEntityResourceMock());

        $this->assertEquals("name2", $property->getColumnName());
    }

    public function testConvertPropertySimpleArrayToXml()
    {
        $a = array("foo" => "bar", "bar" => "baz");
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $property->setPropertyType(Zend_Entity_Mapper_Definition_Property::TYPE_ARRAY);

        $xml = $property->castPropertyToSqlType($a);
        $this->assertEquals(
            '<?xml version="1.0" ?>'.
            '<array><foo><![CDATA[bar]]></foo><bar><![CDATA[baz]]></bar></array>',
            $xml
        );
    }

    public function testConvertPropertyNestedArrayToXml()
    {
        $a = array("foo" => array("bar" => "baz"), "bar" => array("baz", "foo"));
        $property = new Zend_Entity_Mapper_Definition_Property("name1");
        $property->setPropertyType(Zend_Entity_Mapper_Definition_Property::TYPE_ARRAY);

        $xml = $property->castPropertyToSqlType($a);
        $this->assertEquals(
            '<?xml version="1.0" ?>'.
            '<array><foo><bar><![CDATA[baz]]></bar></foo><bar><elem1<![CDATA[baz]]></elem1><elem2><![CDATA[foo]]></bar></array>',
            $xml
        );
    }
}