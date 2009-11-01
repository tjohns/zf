<?php

abstract class Zend_Entity_Definition_RelationTest extends Zend_Entity_Definition_TestCase
{
    /**
     * @return Zend_Entity_Definition_AbstractRelation
     */
    abstract public function createRelation();

    public function testDefaultFetchStrategyIsLazy()
    {
        $relDef = $this->createRelation();

        $this->assertEquals(Zend_Entity_Definition_Property::FETCH_LAZY, $relDef->getFetch());
    }

    public function testClassPublicProperty()
    {
        $relDef = $this->createRelation();
        $relDef->setClass("Foo");

        $this->assertEquals("Foo", $relDef->class);
    }

    public function testFetchPublicProperty()
    {
        $relDef = $this->createRelation();
        $relDef->setFetch(Zend_Entity_Definition_Property::FETCH_SELECT);

        $this->assertEquals(Zend_Entity_Definition_Property::FETCH_SELECT, $relDef->fetch);
    }

    public function testSetFetchStrategyToSelect()
    {
        $relDef = $this->createRelation();
        $relDef->setFetch(Zend_Entity_Definition_Property::FETCH_SELECT);

        $this->assertEquals(Zend_Entity_Definition_Property::FETCH_SELECT, $relDef->getFetch());
    }

    public function testSetFetchStrategyToInvalidNameThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $relDef = $this->createRelation();
        $relDef->setFetch("foo");
    }

    public function testGetCascadeDefaultsToNone()
    {
        $relDef = $this->createRelation();

        $this->assertEquals(array(), $relDef->getCascade());
    }

    public function testSetCascadeInvalidThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relDef = $this->createRelation();
        $relDef->setCascade("foo");
    }

    static public function dataCascade()
    {
        return array(
            array(Zend_Entity_Definition_Property::CASCADE_DELETE, array(Zend_Entity_Definition_Property::CASCADE_DELETE)),
            array(Zend_Entity_Definition_Property::CASCADE_REMOVE, array(Zend_Entity_Definition_Property::CASCADE_REMOVE)),
            array(Zend_Entity_Definition_Property::CASCADE_ALL, array(Zend_Entity_Definition_Property::CASCADE_ALL)),
            array(Zend_Entity_Definition_Property::CASCADE_SAVE, array(Zend_Entity_Definition_Property::CASCADE_SAVE)),
            array(Zend_Entity_Definition_Property::CASCADE_PERSIST, array(Zend_Entity_Definition_Property::CASCADE_PERSIST)),
            array(Zend_Entity_Definition_Property::CASCADE_REFRESH, array(Zend_Entity_Definition_Property::CASCADE_REFRESH)),
            array(Zend_Entity_Definition_Property::CASCADE_DETACH, array(Zend_Entity_Definition_Property::CASCADE_DETACH)),
            array(
                array(Zend_Entity_Definition_Property::CASCADE_REMOVE, Zend_Entity_Definition_Property::CASCADE_SAVE),
                array(Zend_Entity_Definition_Property::CASCADE_REMOVE, Zend_Entity_Definition_Property::CASCADE_SAVE)
            ),
            array(
                array(Zend_Entity_Definition_Property::CASCADE_REFRESH, Zend_Entity_Definition_Property::CASCADE_DETACH),
                array(Zend_Entity_Definition_Property::CASCADE_REFRESH, Zend_Entity_Definition_Property::CASCADE_DETACH),
            ),
        );
    }

    /**
     * @dataProvider dataCascade
     * @param string $option
     * @param array $expected
     */
    public function testCascade($option, $expected)
    {
        $relDef = $this->createRelation();

        $relDef->setCascade($option);
        $this->assertEquals($expected, $relDef->getCascade());
    }

    public function testSetGetColumnName()
    {
        $relDef = $this->createRelation();
        $relDef->setColumnName(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $relDef->getColumnName());
    }
}