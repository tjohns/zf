<?php

class Zend_Entity_LazyLoad_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataAccessingLazyLoadEntityDelegate
     */
    public function testAccessingLazyLoadEntityDelegatesViaCallback($method, $params=array())
    {
        $lazyLoadEntity = $this->createLazyLoadEntity($method);
        call_user_func_array(array($lazyLoadEntity, $method), $params);
    }

    static public function dataAccessingLazyLoadEntityDelegate()
    {
        return array(
            array('getState'),
            array('setState', array(array())),
        );
    }

    public function testAccessViaCallDelegate()
    {
        $lazyLoadEntity = $this->createLazyLoadEntity("getState");
        $lazyLoadEntity->__call("getState", array());
    }

    public function testInvalidCallbackThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $lazyEntity = new Zend_Entity_LazyLoad_Entity('foo', array());
    }

    public function testGetLazyLoadEntityIdIsSecondArgumentField()
    {
        $fixtureLazyLoadEntityId = 1;

        $lazyEntity = new Zend_Entity_LazyLoad_Entity('trim', array('foo', $fixtureLazyLoadEntityId));
        $this->assertEquals($fixtureLazyLoadEntityId, $lazyEntity->getLazyLoadEntityId());
    }

    public function testCreatedLazyLoadEntityIsFalseLoadedFromDb()
    {
        $lazyEntity = $this->createLazyLoadEntity('getState');
        $this->assertFalse($lazyEntity->entityWasLoaded());
    }

    public function testTriggeredLazyLoadEntityIsTrueLoadedFromDb()
    {
        $lazyEntity = $this->createLazyLoadEntity('getState');
        $lazyEntity->__call('getState', array());
        $this->assertTrue($lazyEntity->entityWasLoaded());
    }

    public function createLazyLoadEntity($method)
    {
        $callback = array($this, 'createEntityExpectingMethodOnce');
        $entity = new Zend_Entity_LazyLoad_Entity($callback, array($method, 1));
        return $entity;
    }

    public function createEntityExpectingMethodOnce($method)
    {
        $mock = $this->getMock('Zend_Entity_Interface');
        $mock->expects($this->once())->method($method);
        return $mock;
    }
}