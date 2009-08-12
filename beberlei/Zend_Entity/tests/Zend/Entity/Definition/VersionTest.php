<?php

class Zend_Entity_Definition_VersionTest extends PHPUnit_Framework_TestCase
{
    public function testIsNotNullable()
    {
        $v = new Zend_Entity_Definition_Version("foo");
        $this->assertFalse($v->isNullable());
    }

    public function testIsNotUnique()
    {
        $v = new Zend_Entity_Definition_Version("foo");
        $this->assertFalse($v->isUnique());
    }

    public function dataVersionCastToStorageType()
    {
        return array(
            array("1", 1),
            array("foo", 0),
            array(array(), 0),
        );
    }

    /**
     * @dataProvider dataVersionCastToStorageType
     * @param mixed $value
     * @param int $expectedValue
     */
    public function testCastToStorageTypeAlwaysConvertsToInt($value, $expectedValue)
    {
        $v = new Zend_Entity_Definition_Version("foo");
        $this->assertEquals($expectedValue, $v->castPropertyToStorageType($value));
    }
}