<?php

class Zend_Entity_Mapper_ResultSetMappingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Entity_Mapper_ResultSetMapping
     */
    protected $rsm;

    public function setUp()
    {
        $this->rsm = new Zend_Entity_Mapper_ResultSetMapping();
    }

    public function testAddEntity()
    {
        $this->rsm->addEntity('Foo');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']['properties']));
    }

    public function testAddEntity_IsFluent()
    {
        $this->assertSame($this->rsm, $this->rsm->addEntity('Foo'));
    }

    public function testAddProperty_ToNonExistantEntity_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $this->rsm->addProperty('unknownEntity', 'foo', 'bar');
    }

    public function testAddPropertyToEntity()
    {
        $this->rsm->addEntity('Foo');
        $this->rsm->addProperty('Foo', 'foo', 'bar');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']['properties']['foo']));
        $this->assertEquals('bar', $this->rsm->entityResult['Foo']['properties']['foo']);
    }

    public function testAddProperty_ToEntity_WhichHasAlias_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $this->rsm->addEntity('Foo', 'f');
        $this->rsm->addProperty('Foo', 'foo', 'bar');
    }

    public function testAddProperty_ToEntityAlias()
    {
        $this->rsm->addEntity('Foo', 'f');
        $this->rsm->addProperty('f', 'foo', 'bar');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']['properties']['foo']));
        $this->assertEquals('bar', $this->rsm->entityResult['Foo']['properties']['foo']);
    }

    public function testAddJoinedEntity_AndProperty()
    {
        $this->rsm->addJoinedEntity('Foo', null, null, null);
        $this->rsm->addProperty('Foo', 'bar', 'baz');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']['properties']));

        $this->assertTrue(isset($this->rsm->entityResult['Foo']['properties']['bar']));
        $this->assertEquals('baz', $this->rsm->entityResult['Foo']['properties']['bar']);
    }

    public function testAddJoinedEntity_WithAlias_AndProperty()
    {
        $this->rsm->addJoinedEntity('Foo', 'f', null, null);
        $this->rsm->addProperty('f', 'bar', 'baz');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']['properties']));

        $this->assertTrue(isset($this->rsm->entityResult['Foo']['properties']['bar']));
        $this->assertEquals('baz', $this->rsm->entityResult['Foo']['properties']['bar']);
    }
}