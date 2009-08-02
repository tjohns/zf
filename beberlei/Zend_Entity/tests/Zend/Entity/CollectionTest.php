<?php

class Zend_Entity_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testIteratorBehaviour()
    {
        $initial = array("foo", "bar", "baz");

        $collection = new Zend_Entity_Collection($initial);
        $i = 0;
        foreach($collection AS $key => $element) {
            $this->assertEquals($initial[$i], $element);
            $this->assertEquals($i, $key);
            $i++;
        }
        $i = 0;
        foreach($collection AS $key => $element) {
            $this->assertEquals($initial[$i], $element);
            $this->assertEquals($i, $key);
            $i++;
        }
    }

    public function testCountableBehaviour()
    {
        $data = array("foo", "bar", "baz");

        $collection = new Zend_Entity_Collection($data);
        $this->assertEquals(3, count($collection));
    }

    public function testArrayAccessBehaviour()
    {
        $data = array("foo", "bar", "baz");
        $collection = new Zend_Entity_Collection($data);
        $this->assertEquals("foo", $collection[0]);
        $this->assertEquals("bar", $collection[1]);
        $this->assertEquals("baz", $collection[2]);
        $this->assertTrue(isset($collection[1]));
        $this->assertFalse(isset($collection[3]));

        $collection[4] = "boing";

        $this->assertEquals("boing", $collection[4]);

        unset($collection[4]);

        $this->assertNotEquals("boing", $collection[4]);
    }

    public function testRemoveItem()
    {
        $data = array("foo", "bar", "baz");
        $collection = new Zend_Entity_Collection($data);

        unset($collection[0]);
        $this->assertEquals(2, count($collection));
        foreach($collection AS $element) {
            $this->assertNotEquals("foo", $element);
        }
        $this->assertEquals(array("foo"), $collection->__ze_getRemoved());
    }

    public function testAddItemToCollectionRaisesCount()
    {
        $entity = $this->getMock('Zend_Entity_Interface');
        $collection = new Zend_Entity_Collection();
        $collection[] = $entity;

        $this->assertEquals(1, count($collection));
    }

    public function testAddedItemsAccessesAllNewItems()
    {
        $entity = $this->getMock('Zend_Entity_Interface');
        $collection = new Zend_Entity_Collection();
        $collection[] = $entity;

        $addedItems = $collection->__ze_getAdded();

        $this->assertTrue(isset($addedItems[0]));
        $this->assertEquals($entity, $addedItems[0]);
    }

    public function testRestrictedToClassCollectionThrowsExceptionOnForeignClassAdd()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $otherClassType = $this->getMock('Zend_Entity_Interface');
        $collection = new Zend_Entity_Collection(array(), "someClassType");
        $collection[] = $otherClassType;
    }
}