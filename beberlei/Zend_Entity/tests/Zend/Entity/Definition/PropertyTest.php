<?php

class Zend_Entity_Definition_PropertyTest extends Zend_Entity_Definition_TestCase
{
    public function testSetPropertyNameViaConstructor()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $this->assertEquals("name1", $property->getPropertyName());
    }

    public function testPropertyNamePublicProperty()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $this->assertEquals("name1", $property->propertyName);
    }

    public function testResetPropertyNameWithMethod()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setPropertyName("name2");
        $this->assertEquals("name2", $property->getPropertyName());
    }

    public function testSetGetColumnName()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setColumnName("name2");
        $this->assertEquals("name2", $property->getColumnName());
    }

    public function testColumnNamePublicProperty()
    {
        $property = new Zend_Entity_Definition_Property("name1");

        $this->assertEquals(null, $property->columnName);
        $property->setColumnName("name2");
        $this->assertEquals("name2", $property->columnName);
    }

    public function testSetColumnNameConstructor()
    {
        $property = new Zend_Entity_Definition_Property("name1", array("columnName" => "name2"));
        $this->assertEquals("name2", $property->getColumnName());
    }

    public function testGetDefaultPropertyType()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $this->assertEquals(Zend_Entity_Definition_Property::TYPE_STRING, $property->propertyType);
        $this->assertEquals(Zend_Entity_Definition_Property::TYPE_STRING, $property->getPropertyType());
    }

    public function testSetGetPropertyType()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setPropertyType("asfd");
        $this->assertEquals("asfd", $property->getPropertyType());
    }

    public function testPropertyTypePublicProperty()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setPropertyType("asfd");

        $this->assertEquals("asfd", $property->propertyType);
    }

    public function testSetPropertyTypeConstructor()
    {
        $property = new Zend_Entity_Definition_Property("name1", array("propertyType" => "asdf"));
        $this->assertEquals("asdf", $property->getPropertyType());
    }

    public function testGetNullableDefault()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $this->assertFalse($property->isNullable());
    }

    public function testSetNullable()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setNullable(true);

        $this->assertTrue($property->isNullable());

        $property->setNullable(false);
        $this->assertFalse($property->isNullable());
    }

    public function testGetUniqueDefault()
    {
        $property = new Zend_Entity_Definition_Property("name1");

        $this->assertFalse($property->isUnique());
    }

    public function testSetUnique()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setUnique(true);

        $this->assertTrue($property->isUnique());

        $property->setUnique(false);
        $this->assertFalse($property->isUnique());
    }

    public function testConvertPropertyType_ReturnsNull_IfNullable()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setNullable(true);

        $this->assertNull($property->castPropertyToStorageType(null));
    }

    public function testConvertStorageType_ReturnsNull_IfNullable()
    {
        $property = new Zend_Entity_Definition_Property("name1");
        $property->setNullable(true);

        $this->assertNull($property->castColumnToPhpType(null));
    }
}