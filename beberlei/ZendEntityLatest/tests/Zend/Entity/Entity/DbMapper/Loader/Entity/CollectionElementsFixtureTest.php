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

    private function setElementCollection_UseSelectFetch()
    {
        $this->mappings["Zend_TestEntity1"]
             ->elementCollections["elements"]
             ->setFetch("select");
    }

    public function testCreateEntity_BuildSelectQueryToRetrieveElements()
    {
        $this->loader = $this->createLoader();
        $this->setElementCollection_UseSelectFetch();
        $this->adapter->getProfiler()->setEnabled(true);

        $row = array("id" => 1);
        $entity = $this->loader->createEntityFromState($row, $this->mappings["Zend_TestEntity1"]);

        $qp = $this->adapter->getProfiler()->getLastQueryProfile();
        /* @var $qp Zend_Db_Profiler_Query */
        $this->assertEquals('SELECT entities_elements.* FROM entities_elements WHERE (fk_id = 1)', $qp->getQuery());
    }

    public function testCreateEntity_CollectionsElements_LazyFetch()
    {
        $this->loader = $this->createLoader();

        $rows = array(
            array("col_key" => "foo", "col_name" => "bar"),
            array("col_key" => "bar", "col_name" => "baz")
        );

        $stmt = Zend_Test_DbStatement::createSelectStatement($rows);
        $this->adapter->appendStatementToStack($stmt);

        $row = array("id" => 1);

        $entity = $this->loader->createEntityFromState($row, $this->mappings["Zend_TestEntity1"]);

        $this->assertType("Zend_Entity_LazyLoad_Array", $entity->elements);
        $this->assertType("Zend_Entity_Collection_Array", $entity->elements);
        $this->assertTrue(isset($entity->elements["foo"]));
        $this->assertTrue(isset($entity->elements["bar"]));
        $this->assertEquals("bar", $entity->elements["foo"]);
        $this->assertEquals("baz", $entity->elements["bar"]);
    }

    public function testCreateEntity_SaveRelatedArrayIntoIdentityMap()
    {
        $this->loader = $this->createLoader();
        $this->adapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(array()));

        $row = array("id" => 1);

        $entity = $this->loader->createEntityFromState($row, $this->mappings["Zend_TestEntity1"]);

        $array = $this->identityMap->getRelatedObject($entity, "elements");

        $this->assertType('Zend_Entity_LazyLoad_Array', $array);
    }

    public function testCreateEntity_CollectionElements_SelectFetch()
    {
        $this->loader = $this->createLoader();
        $this->setElementCollection_UseSelectFetch();

        $rows = array(
            array("col_key" => "foo", "col_name" => "bar"),
            array("col_key" => "bar", "col_name" => "baz")
        );

        $stmt = Zend_Test_DbStatement::createSelectStatement($rows);
        $this->adapter->appendStatementToStack($stmt);

        $row = array("id" => 1);

        $entity = $this->loader->createEntityFromState($row, $this->mappings["Zend_TestEntity1"]);

        $this->assertNotType("Zend_Entity_LazyLoad_Array", $entity->elements);
        $this->assertType("Zend_Entity_Collection_Array", $entity->elements);
        $this->assertTrue(isset($entity->elements["foo"]));
        $this->assertTrue(isset($entity->elements["bar"]));
        $this->assertEquals("bar", $entity->elements["foo"]);
        $this->assertEquals("baz", $entity->elements["bar"]);
    }
}