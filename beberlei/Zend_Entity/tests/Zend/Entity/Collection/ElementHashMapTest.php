<?php

class Zend_Entity_Collection_ElementHashMapTest extends PHPUnit_Framework_TestCase
{
    public function createElementHashMap()
    {
        return new Zend_Entity_Collection_ElementHashMap(
            array("foo" => "bar", "bar" => "baz")
        );
    }

    public function testIterator()
    {
        $hashMap = $this->createElementHashMap();

        $i = 0;
        foreach($hashMap AS $k => $v) {
            $this->assertTrue(in_array($k, array("foo", "bar")));
            $this->assertTrue(in_array($v, array("bar", "baz")));
            $i++;
        }
        $this->assertEquals(2, $i);
        foreach($hashMap AS $k => $v) {
            $this->assertTrue(in_array($k, array("foo", "bar")));
            $this->assertTrue(in_array($v, array("bar", "baz")));
            $i++;
        }
        $this->assertEquals(4, $i);
    }

    public function testArrayAccess()
    {
        $hashMap = $this->createElementHashMap();

        $this->assertTrue(isset($hashMap['foo']));
        $this->assertTrue(isset($hashMap['bar']));
        $this->assertEquals("bar", $hashMap['foo']);
        $this->assertEquals("baz", $hashMap['bar']);

        unset($hashMap['foo']);
        $this->assertFalse(isset($hashMap['foo']));

        $hashMap['foo'] = "hello";
        $this->assertTrue(isset($hashMap['foo']));
        $this->assertEquals("hello", $hashMap['foo']);
    }

    public function testArrayAccess_UnknownElement_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $hashMap = $this->createElementHashMap();
        $invalid = $hashMap['invalid'];
    }

    public function testCountable()
    {
        $hashMap = $this->createElementHashMap();

        $this->assertEquals(2, count($hashMap));
    }

    public function testRetrieveAddedElements()
    {
        $hashMap = $this->createElementHashMap();

        $hashMap['baz'] = 1;
        $hashMap['hello'] = 'world';

        $this->assertEquals(
            array('baz' => 1, 'hello' => 'world'),
            $hashMap->__ze_getAdded()
        );
    }

    public function testRetrieveRemovedElements()
    {
        $hashMap = $this->createElementHashMap();

        unset($hashMap['foo']);
        unset($hashMap['bar']);

        $this->assertEquals(
            array('foo' => 'foo', 'bar' => 'bar'),
            $hashMap->__ze_getRemoved()
        );
    }

    public function testEditExistingElement_AddsToBothRemoveAndAdded()
    {
        $hashMap = $this->createElementHashMap();

        $hashMap['foo'] = "baz";
        $hashMap['bar'] = "bar";

        $this->assertEquals(
            array('foo' => 'foo', 'bar' => 'bar'),
            $hashMap->__ze_getRemoved()
        );

        $this->assertEquals(
            array('foo' => "baz", 'bar' => 'bar'),
            $hashMap->__ze_getAdded()
        );
    }

    static public function dataAllowedTypes()
    {
        return array(
            array(1), array("foo"), array(1.32), array(true), array(false), array(0), array(""),
        );
    }

    /**
     * @dataProvider dataAllowedTypes
     * @param mixed $value
     */
    public function testSetAllowedTypes($value)
    {
        $hashMap = $this->createElementHashMap();
        $hashMap["foo"] = $value;

        $this->assertEquals($value, $hashMap["foo"]);
    }

    static public function dataInvalidTypes()
    {
        return array(
            array(new stdClass()),
            array(array()),
        );
    }

    /**
     * @dataProvider dataInvalidTypes
     * @param mixed $value
     */
    public function testSetInvalidTypes($value)
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $hashMap = $this->createElementHashMap();
        $hashMap["foo"] = $value;
    }

    static public function dataInvalidKeys()
    {
        return array(
            array(new stdClass()),
            array(array()),
            array(1),
            array(0),
            array(1.32),
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider dataInvalidKeys
     * @param mixed $index
     */
    public function testSetInvalidKeys($index)
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $hashMap = $this->createElementHashMap();
        $hashMap[$index] = "foo";
    }

    public function testWasLoadedFromDatabase()
    {
        $hashMap = $this->createElementHashMap();
        $this->assertTrue($hashMap->__ze_wasLoadedFromDatabase());
    }
}