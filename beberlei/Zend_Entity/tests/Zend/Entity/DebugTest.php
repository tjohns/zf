<?php

class Zend_Entity_DebugTest extends PHPUnit_Framework_TestCase
{
    public function testDumpLazyLoadCollection()
    {
        $lazyLoadCollection = $this->createLazyLoadCollectionExpectingWasLoadedFromDbWithFalse();

        $expected = 'string(20) "*LAZYLOADCOLLECTION*"
';
        $this->assertDebugOutputEquals($expected, $lazyLoadCollection);
    }

    public function testDumpEntityWithoutLazyLoadProperties()
    {
        $entity = $this->createEntityThatReturnsState(array('foo' => 'bar'));
        $expected = 'array(1) {
  ["foo"]=>
  string(3) "bar"
}
';
        $this->assertDebugOutputEquals($expected, $entity);
    }

    public function testDumpEntityWithLazyLoadEntity()
    {
        $lazyLoadEntity = new Zend_Entity_LazyLoad_Entity('rand', array(0, 1));
        $entity = $this->createEntityThatReturnsState(array('foo' => $lazyLoadEntity));

        $expected = 'array(1) {
  ["foo"]=>
  string(16) "*LAZYLOADENTITY*"
}
';
        $this->assertDebugOutputEquals($expected, $entity);
    }

    public function testDumpEntityWithLazyLoadCollection()
    {
        $lazyLoadCollection = $this->createLazyLoadCollectionExpectingWasLoadedFromDbWithFalse();
        $entity = $this->createEntityThatReturnsState(array('foo' => $lazyLoadCollection));

        $expected = 'array(1) {
  ["foo"]=>
  string(20) "*LAZYLOADCOLLECTION*"
}
';
        $this->assertDebugOutputEquals($expected, $entity);
    }

    public function testDumpEntityCollectionThatIsNotLazyLoaded()
    {
        $entities = array();
        $entities[] = $this->createEntityThatReturnsState(array("foo" => "bar"));
        $entities[] = $this->createEntityThatReturnsState(array("foo" => "baz"));
        $collection = new Zend_Entity_Collection($entities);

        $expected = 'array(1) {
  ["foo"]=>
  string(3) "bar"
}
array(1) {
  ["foo"]=>
  string(3) "baz"
}
';
        $this->assertDebugOutputEquals($expected, $collection);
    }

    public function createEntityThatReturnsState($state)
    {
        $entity = $this->getMock('Zend_Entity_Interface');
        $entity->expects($this->once())->method('getState')->will($this->returnValue($state));
        return $entity;
    }

    public function createLazyLoadCollectionExpectingWasLoadedFromDbWithFalse()
    {
        $lazyLoadCollection = $this->getMock('Zend_Entity_Collection_Interface');
        $lazyLoadCollection->expects($this->once())->method('wasLoadedFromDatabase')->will($this->returnValue(false));
        return $lazyLoadCollection;
    }

    public function assertDebugOutputEquals($expected, $object)
    {
        ob_start();
        Zend_Entity_Debug::dump($object);
        $output = ob_get_clean();
        $this->assertEquals($expected, $output);
    }
}