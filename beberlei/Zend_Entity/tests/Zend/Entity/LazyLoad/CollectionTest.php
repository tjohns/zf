<?php

class Zend_Entity_LazyLoad_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testCreateCollectionWithValidCallbackDoesNotThrowException()
    {
        $validCallback = 'substr';
        $collection = new Zend_Entity_LazyLoad_Collection($validCallback, array());
    }

    public function testCreateCollectionWithInvalidCallbackThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $invalidCallback = array('invalidClass', 'invalidMethod');
        $collection = new Zend_Entity_LazyLoad_Collection($invalidCallback, array());
    }

    public function testIsNotLoadedByDefault()
    {
        $collection = new Zend_Entity_LazyLoad_Collection('substr', array());
        $this->assertFalse($collection->wasLoadedFromDatabase());
    }

    public function testValidCallbackReturningNonCollectionThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $callback = 'rand';
        $args = array(0, 1);
        $collection = new Zend_Entity_LazyLoad_Collection($callback, $args);
        $collection->key();
    }

    /**
     *
     * @dataProvider dataInnerCollectionDelegate
     */
    public function testInnerCollectionDelegateOf($method, $params=array())
    {
        $collection = $this->createLazyLoadCollection($method);
        call_user_func_array(array($collection, $method), $params);
    }

    static public function dataInnerCollectionDelegate()
    {
        return array(
            array('key'),
            array('next'),
            array('valid'),
            array('current'),
            array('rewind'),
            array('count'),
            array('offsetExists', array('foo')),
            array('offsetGet', array('foo')),
            array('offsetSet', array('foo', 'bar')),
            array('offsetUnset', array('foo')),
            array('add', array(1)),
            array('remove', array(1)),
            array('getAdded'),
            array('getRemoved'),
        );
    }

    public function createLazyLoadCollection($methodName, $returnValue=false)
    {
        $callback = array($this, 'createInnerCollecctionMockThatExpectsOnce');
        $args = array($methodName, $returnValue);
        return new Zend_Entity_LazyLoad_Collection($callback, $args);
    }

    public function createInnerCollecctionMockThatExpectsOnce($methodName, $returnValue=true)
    {
        $innerCollection = $this->getMock('Zend_Entity_Collection_Interface');
        $innerCollection->expects($this->once())->method($methodName)->will($this->returnValue($returnValue));

        return $innerCollection;
    }
}