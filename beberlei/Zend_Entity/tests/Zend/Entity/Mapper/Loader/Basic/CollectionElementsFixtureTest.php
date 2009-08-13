<?php

class Zend_Entity_Mapper_Loader_Basic_CollectionElementsFixtureTest extends Zend_Entity_Mapper_Loader_TestCase
{
    public function setUp()
    {
        $this->fixture = new Zend_Entity_Fixture_CollectionElementDefs();
        $mi = $this->fixture->getResourceMap()->transform('Zend_Entity_Mapper_MappingInstruction');
        $this->loader = new Zend_Entity_Mapper_Loader_Basic(
            $this->fixture->getEntityDefinition('Zend_TestEntity1'),
            $mi["Zend_TestEntity1"]
        );
    }

    public function testLoadRow_CollectionElementsHydration()
    {
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

        $em = new Zend_Entity_Manager($dbMock);

        $row = array("id" => 1);

        $entity = new Zend_TestEntity1();

        $this->loader->loadRow($entity, $row, $em);

        $this->assertEquals("bar", $entity->elements["foo"]);
        $this->assertEquals("baz", $entity->elements["bar"]);
    }
}