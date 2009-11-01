<?php

class Zend_Entity_StateTransformer_TypeConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Entity_StateTransformer_TypeConverter
     */
    public $converter = null;

    public function setUp()
    {
        $this->converter = new Zend_Entity_StateTransformer_TypeConverter();
    }

    static public function dataTypes()
    {
        return array(
            array('string'),
            array('int'),
            array('bool'),
            array('float'),
            array('datetime'),
            array('date'),
            array('timestamp'),
            array('array'),
        );
    }

    /**
     * @dataProvider dataTypes
     * @param string $type
     */
    public function testConvertPropertyType_ReturnsNull_IfNullable($type)
    {
        $this->assertNull($this->converter->convertToStorageType($type, null, true));
    }

    /**
     * @dataProvider dataTypes
     * @param string $type
     */
    public function testConvertStorageType_ReturnsNull_IfNullable($type)
    {
        $this->assertNull($this->converter->convertToPhpType($type, null, true));
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
        $this->assertEquals(
            $expectedPhpValue,
            $this->converter->convertToPhpType($type, $storageValue, false)
        );
    }

    public function testCovertStorageToDateType()
    {
        $date = $this->converter->convertToPhpType('date', '2009-01-01', false);

        $this->assertType('datetime', $date);
        $this->assertEquals('2009-01-01', $date->format('Y-m-d'));
    }

    public function testConvertStorageToDateTimeType()
    {
        $date = $this->converter->convertToPhpType('datetime', '2009-01-01 10:10:10', false);

        $this->assertType('datetime', $date);
        $this->assertEquals('2009-01-01', $date->format('Y-m-d'));
        $this->assertEquals('2009-01-01 10:10:10', $date->format('Y-m-d H:i:s'));
    }

    public function testConvertStorageToTimestampType()
    {
        $ts = mktime(10, 10, 10, 1, 1, 2009);
        $date = $this->converter->convertToPhpType('timestamp', $ts, false);

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
        $this->assertEquals(
            $expectedStorageValue,
            $this->converter->convertToStorageType($type, $phpValue, false)
        );
    }

    public function testConvertDateTimeToStorageType()
    {
        $this->assertEquals(
            "2009-01-01 10:10:10",
            $this->converter->convertToStorageType('datetime', new DateTime("2009-01-01 10:10:10"), false)
        );
    }

    public function testConvertDateToStorageType()
    {
        $this->assertEquals(
            "2009-01-01",
            $this->converter->convertToStorageType('date', new DateTime("2009-01-01 10:10:10"), false)
        );
    }

    public function testConvertTimestampToStorageType()
    {
        $this->assertEquals(
            mktime(10, 10, 10, 1, 1, 2009),
            $this->converter->convertToStorageType('timestamp', new DateTime("2009-01-01 10:10:10"), false)
        );
    }

    public function testConvertXmlArrayToPhpArray()
    {
        $columnValue = '<?xml version="1.0" ?><array type="array"><array_numeric_elem0 type="string">foo</array_numeric_elem0>'.
            '<array_numeric_elem1 type="string">bar</array_numeric_elem1></array>';
        $this->assertEquals(
            array("foo", "bar"),
            $this->converter->convertToPhpType('array', $columnValue, false)
        );
    }

    public function testConvertPhpArrayToXmlArray()
    {
        $columnValue = '<?xml version="1.0" ?>'."\n".'<array type="array"><array_numeric_elem0 type="string"><![CDATA[foo]]></array_numeric_elem0>'.
            '<array_numeric_elem1 type="string"><![CDATA[bar]]></array_numeric_elem1></array>';
        $this->assertEquals(
            $columnValue,
            $this->converter->convertToStorageType('array', array("foo", "bar"), false)
        );
    }
}
