<?php

class Zend_Entity_Event_CallbackEvent
{
    public $executed = true;
    private $returnValue;

    public function __construct($returnValue)
    {
        $this->returnValue = $returnValue;
    }

    public function execute()
    {
        $this->executed = true;
        return $this->returnValue;
    }
}

class Zend_Entity_Event_ListenerTest extends PHPUnit_Framework_TestCase
{
    const EVENT_SUCCESS = true;
    const EVENT_FAILURE = false;

    /**
     * @var Zend_Entity_Event_Listener
     */
    private $listener = null;

    public function setUp()
    {
        $this->listener = new Zend_Entity_Event_Listener();
        $this->entity = new Zend_TestEntity1();
    }

    public function attachEventMock($method, $returnValue)
    {
        $eventMock = $this->getMock('Zend_Entity_Event_EventAbstract');
        $eventMock->expects($this->once())
                  ->method($method)
                  ->with($this->equalTo($this->entity))
                  ->will($this->returnValue($returnValue));
        $this->listener->registerEvent($eventMock);
    }

    public function triggerEvent($method)
    {
        return $this->listener->$method($this->entity);
    }

    static public function dataEvents()
    {
        return array(
            array('preInsert'),
            array('postInsert'),
            array('preUpdate'),
            array('postUpdate'),
            array('preDelete'),
            array('postDelete'),
            array('postLoad')
        );
    }

    /**
     * @dataProvider dataEvents
     * @param string $eventType
     */
    public function testEventSuccess($eventType)
    {
        $this->attachEventMock($eventType, self::EVENT_SUCCESS);
        $this->assertTrue($this->triggerEvent($eventType));
    }

    /**
     * @dataProvider dataEvents
     * @param string $eventType
     */
    public function testEventFailure($eventType)
    {
        $this->attachEventMock($eventType, self::EVENT_FAILURE);
        $this->assertFalse($this->triggerEvent($eventType));
    }

    /**
     * @dataProvider dataEvents
     * @param string $eventType
     */
    public function testCallbackSuccess($eventType)
    {
        $event = new Zend_Entity_Event_CallbackEvent(self::EVENT_SUCCESS);
        $this->listener->registerCallback($eventType, array($event, 'execute'));
        $ret = $this->triggerEvent($eventType);
        $this->assertTrue($event->executed);
        $this->assertTrue($ret);
    }

    /**
     * @dataProvider dataEvents
     * @param string $eventType
     */
    public function testCallbackFailure($eventType)
    {
        $event = new Zend_Entity_Event_CallbackEvent(self::EVENT_FAILURE);
        $this->listener->registerCallback($eventType, array($event, 'execute'));
        $ret = $this->triggerEvent($eventType);
        $this->assertTrue($event->executed);
        $this->assertFalse($ret);
    }

    public function testInvalidCallback_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $this->listener->registerCallback("postLoad", "invalidCallback");
    }
}