<?php

class Zend_Entity_StateTransformer_PropertyTest extends PHPUnit_Framework_TestCase
{
    public $stateTransformer = null;

    public function setUp()
    {
        $this->stateTransformer = new Zend_Entity_StateTransformer_Property();
    }

    public function fakeAnObject()
    {
        $o = new Zend_Entity_StateTransformer_PropertyEntity();
        $o->a = 1;
        $o->b = "foo";
        return $o;
    }

    public function testGetState_OfUnitializedTransformer_ReturnsEmptyArray()
    {
        $o = $this->fakeAnObject();
        $this->assertEquals(array(), $this->stateTransformer->getState($o));
    }

    public function testGetState()
    {
        $this->stateTransformer->setTargetClass('Zend_Entity_StateTransformer_PropertyEntity', array('a', 'b'));

        $o = $this->fakeAnObject();
        
        $this->assertEquals(array('a' => 1, 'b' => 'foo'), $this->stateTransformer->getState($o));
    }

    public function testSetState()
    {
        $this->stateTransformer->setTargetClass('Zend_Entity_StateTransformer_PropertyEntity', array('a', 'b'));

        $o =  new Zend_Entity_StateTransformer_PropertyEntity();

        $this->stateTransformer->setState($o, array('a' => 1, 'b' => 'foo'));

        $this->assertEquals(1, $o->a);
        $this->assertEquals("foo", $o->b);
    }

    public function testIdField()
    {
        $this->stateTransformer->setTargetClass('Zend_Entity_StateTransformer_PropertyEntity', array('a', 'b'));

        $o =  new Zend_Entity_StateTransformer_PropertyEntity();

        $this->stateTransformer->setId($o, "a", 10);

        $this->assertEquals(10, $o->a);
    }

    public function testSetTargetClass_ClassMissesAProperty_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_StateTransformer_Exception");

        $this->stateTransformer->setTargetClass('stdClass', array('a', 'b'));
    }

    public function testSetTargetClass_PrivateOrProtectedProperty_PhpLessThan53_ThrowsException()
    {
        if (version_compare(PHP_VERSION, '5.3.0') === 1) {
            $this->markTestSkipped("Test checks behaviour for PHP versions previous to 5.3.");
        }

        $this->setExpectedException("Zend_Entity_StateTransformer_Exception");

        $this->stateTransformer->setTargetClass('Zend_Entity_StateTransformer_PropertyEntity', array('a', 'b', 'c'));
    }

    public function testSetTargetClass_PrivateOrProtectedProperty_Php53_SetAccessible()
    {
        if (version_compare(PHP_VERSION, '5.3.0') !== 1) {
            $this->markTestSkipped("Test will only run on PHP versions beginning with 5.3.0");
        }

        $this->stateTransformer->setTargetClass('Zend_Entity_StateTransformer_PropertyEntity', array('a', 'b', 'c'));

        $o = $this->fakeAnObject();

        $this->assertEquals(array('a' => 1, 'b' => 'foo', 'c' => 'bar'), $this->stateTransformer->getState($o));
    }

    public function testSetState_Php53()
    {
        if (version_compare(PHP_VERSION, '5.3.0') !== 1) {
            $this->markTestSkipped("Test will only run on PHP versions beginning with 5.3.0");
        }

        $this->stateTransformer->setTargetClass('Zend_Entity_StateTransformer_PropertyEntity', array('a', 'b', 'c'));

        $o =  new Zend_Entity_StateTransformer_PropertyEntity();

        $this->stateTransformer->setState($o, array('a' => 1, 'b' => 'foo', 'c' => 'bar'));

        $this->assertEquals(array('a' => 1, 'b' => 'foo', 'c' => 'bar'), $this->stateTransformer->getState($o));
    }

    public function testSetId_Php53()
    {
        if (version_compare(PHP_VERSION, '5.3.0') !== 1) {
            $this->markTestSkipped("Test will only run on PHP versions beginning with 5.3.0");
        }

        $this->stateTransformer->setTargetClass('Zend_Entity_StateTransformer_PropertyEntity', array('a', 'b', 'c'));

        $o =  new Zend_Entity_StateTransformer_PropertyEntity();

        $this->stateTransformer->setId($o, 'c', 'baz');

        $this->assertEquals(array('a' => null, 'b' => null, 'c' => 'bar'), $this->stateTransformer->getState($o));
    }
}

class Zend_Entity_StateTransformer_PropertyEntity
{
    public $a;
    public $b;
    protected $c = "bar";
}