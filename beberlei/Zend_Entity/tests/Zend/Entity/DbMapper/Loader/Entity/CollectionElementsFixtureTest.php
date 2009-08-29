<?php

class Zend_Entity_DbMapper_Loader_Entity_CollectionElementsFixtureTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_CollectionElementDefs";
    }

    public function testLoadRow_LazyCollectionElementsHydration()
    {
        $this->loader = $this->createLoader();

        $rows = array(
            array("col_key" => "foo", "col_name" => "bar"),
            array("col_key" => "bar", "col_name" => "baz")
        );

        $stmtMock = $this->getMock('Zend_Db_Statement_Interface');
        $stmtMock->expects($this->once())
             ->method('fetchAll')
             ->will($this->returnValue($rows));

        $selectMock = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $selectMock->expects($this->at(0))
                   ->method('from')
                   ->with($this->equalTo('entities_elements'));
        $selectMock->expects($this->at(1))
                   ->method('where')
                   ->with($this->equalTo('fk_id = ?'), $this->equalTo(1));
        $selectMock->expects($this->at(2))
                   ->method('query')
                   ->will($this->returnValue($stmtMock));

        $dbMock = $this->getMock('Zend_Db_Adapter_Mysqli', array(), array(), '', false);
        $dbMock->expects($this->once())
               ->method('select')
               ->will($this->returnValue($selectMock));

        $em = new Zend_Entity_Manager(array('adapter' => $dbMock));

        $row = array("id" => 1);

        $entity = new Zend_TestEntity1();

        $this->loader->loadRow($entity, $row, $this->mappings["Zend_TestEntity1"]);

        $this->assertType("Zend_Entity_LazyLoad_ElementHashMap", $entity->elements);
        $this->assertType("Zend_Entity_Collection_ElementHashMap", $entity->elements);
        $this->assertEquals("bar", $entity->elements["foo"]);
        $this->assertEquals("baz", $entity->elements["bar"]);
    }

    public function testLoadRow_SelectCollectionElementsHydration()
    {
        $this->mappings["Zend_TestEntity1"]->elementCollections["elements"]->fetch = "select";
        $this->loader = $this->createLoader();

        $rows = array(
            array("col_key" => "foo", "col_name" => "bar"),
            array("col_key" => "bar", "col_name" => "baz")
        );

        $stmtMock = $this->getMock('Zend_Db_Statement_Interface');
        $stmtMock->expects($this->once())
             ->method('fetchAll')
             ->will($this->returnValue($rows));

        $selectMock = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $selectMock->expects($this->at(0))
                   ->method('from')
                   ->with($this->equalTo('entities_elements'));
        $selectMock->expects($this->at(1))
                   ->method('where')
                   ->with($this->equalTo('fk_id = ?'), $this->equalTo(1));
        $selectMock->expects($this->at(2))
                   ->method('query')
                   ->will($this->returnValue($stmtMock));

        $dbMock = $this->getMock('Zend_Db_Adapter_Mysqli', array(), array(), '', false);
        $dbMock->expects($this->once())
               ->method('select')
               ->will($this->returnValue($selectMock));

        $em = new Zend_Entity_Manager(array('adapter' => $dbMock));

        $row = array("id" => 1);

        $entity = new Zend_TestEntity1();

        $this->loader->loadRow($entity, $row, $this->mappings["Zend_TestEntity1"]);

        $this->assertNotType("Zend_Entity_LazyLoad_ElementHashMap", $entity->elements);
        $this->assertType("Zend_Entity_Collection_ElementHashMap", $entity->elements);
        $this->assertEquals("bar", $entity->elements["foo"]);
        $this->assertEquals("baz", $entity->elements["bar"]);
    }
}