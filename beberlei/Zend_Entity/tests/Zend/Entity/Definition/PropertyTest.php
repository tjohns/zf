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

    public function testCreateWithArrayAsFirstArgument()
    {
        $property = new Zend_Entity_Definition_Property(array("propertyName" => "name1", "columnName" => "name2"));

        $this->assertEquals("name1", $property->propertyName);
        $this->assertEquals("name2", $property->columnName);
    }

    static public function dataConvertStorageToPhpType()
    {
        return array(
            array("string", "foo", "foo"),
            array("string", 1, "1"),
            array("string", null, ""),
            array("int", 1, 1),
            array("int", "1", 1),
            array("int", null, 0),
            array("int", "foo", 0),
            array("float", 1.23, 1.23),
            array("float", "1.23", 1.23),
            array("float", "1", 1),
            array("float", null, 0),
            array("float", "foo", 0),
            array("bool", true, true),
            array("bool", 1, true),
            array("bool", 2, false),
            array("bool", false, false),
            array("bool", "2", false),
            array("bool", "foo", false),
        );
    }

    /**
     * @dataProvider dataConvertStorageToPhpType
     * @param string $type
     * @param mixed $storageValue
     * @param mixed $expectedPhpValue
     */
    public function testCovertStorageToPhpType($type, $storageValue, $expectedPhpValue)
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => $type,
                "nullable" => false,
            )
        );
        $this->assertEquals($expectedPhpValue, $property->castColumnToPhpType($storageValue));
    }

    public function testCovertStorageToDateType()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'date',
                "nullable" => false,
            )
        );
        $date = $property->castColumnToPhpType('2009-01-01');

        $this->assertType('datetime', $date);
        $this->assertEquals('2009-01-01', $date->format('Y-m-d'));
    }

    public function testConvertStorageToDateTimeType()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'datetime',
                "nullable" => false,
            )
        );
        $date = $property->castColumnToPhpType('2009-01-01 10:10:10');

        $this->assertType('datetime', $date);
        $this->assertEquals('2009-01-01', $date->format('Y-m-d'));
        $this->assertEquals('2009-01-01 10:10:10', $date->format('Y-m-d H:i:s'));
    }

    public function testConvertStorageToTimestampType()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'timestamp',
                "nullable" => false,
            )
        );

        $ts = mktime(10, 10, 10, 1, 1, 2009);
        $date = $property->castColumnToPhpType($ts);

        $this->assertType('datetime', $date);
        $this->assertEquals($ts, $date->format('U'));
    }

    static public function dataConvertPhpToStorageType()
    {
        return array(
            array("string", "foo", "foo"),
            array("string", 1234, "1234"),
            array("string", 123.4, "123.4"),
            array("string", null, ""),
            array("string", false, ""),
            array("string", true, "1"),
            array("int", 123, 123),
            array("int", "123", 123),
            array("int", false, 0),
            array("int", true, 1),
            array("int", 123.1, 123),
            array("int", null, 0),
            array("float", 123.3, 123.3),
            array("float", "123", 123),
            array("float", "123.4", 123.4),
            array("float", null, 0),
            array("float", "foo", 0),
            array("bool", true, 1),
            array("bool", "foo", 0),
            array("bool", false, 0),
            array("bool", "1", 0),
        );
    }

    /**
     * @dataProvider dataConvertPhpToStorageType
     * @param string $type
     * @param mixed $phpValue
     * @param mixed $expectedStorageValue
     */
    public function testConvertPhpToStorageType($type, $phpValue, $expectedStorageValue)
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => $type,
                "nullable" => false,
            )
        );

        $this->assertEquals($expectedStorageValue, $property->castPropertyToStorageType($phpValue));
    }

    public function testConvertDateTimeToStorageType()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'datetime',
                "nullable" => false,
            )
        );

        $this->assertEquals("2009-01-01 10:10:10", $property->castPropertyToStorageType(new DateTime("2009-01-01 10:10:10")));
    }

    public function testConvertDateToStorageType()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'date',
                "nullable" => false,
            )
        );

        $this->assertEquals("2009-01-01", $property->castPropertyToStorageType(new DateTime("2009-01-01 10:10:10")));
    }

    public function testConvertTimestampToStorageType()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'timestamp',
                "nullable" => false,
            )
        );

        $this->assertEquals(mktime(10, 10, 10, 1, 1, 2009), $property->castPropertyToStorageType(new DateTime("2009-01-01 10:10:10")));
    }

    public function testConvertXmlArrayToPhpArray()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'array',
                "nullable" => false,
            )
        );

        $columnValue = '<?xml version="1.0" ?><array type="array"><array_numeric_elem0 type="string">foo</array_numeric_elem0>'.
            '<array_numeric_elem1 type="string">bar</array_numeric_elem1></array>';
        $this->assertEquals(array("foo", "bar"), $property->castColumnToPhpType($columnValue));
    }


    public function testConvertPhpArrayToXmlArray()
    {
        $property = new Zend_Entity_Definition_Property(
            array(
                "propertyName" => "foo",
                "propertyType" => 'array',
                "nullable" => false,
            )
        );

        $columnValue = '<?xml version="1.0" ?>'."\n".'<array type="array"><array_numeric_elem0 type="string"><![CDATA[foo]]></array_numeric_elem0>'.
            '<array_numeric_elem1 type="string"><![CDATA[bar]]></array_numeric_elem1></array>';
        $this->assertEquals($columnValue, $property->castPropertyToStorageType(array("foo", "bar")));
    }
}
