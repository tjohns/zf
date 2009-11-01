<?php

class Zend_Entity_DbMapper_Persister_ArrayTest extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_Manager_Interface
     */
    public $entityManager;

    /**
     * @var Zend_Test_DbAdapter
     */
    public $testAdapter;

    /**
     *
     * @var Zend_Db_Mapper_Persister_Array
     */
    public $persister;

    public function setUp()
    {
        $this->testAdapter = new Zend_Test_DbAdapter();
        $this->entityManager = $this->createEntityManager(null, null, null, $this->testAdapter);

        $arrayDef = new Zend_Entity_Definition_Array("array", array(
            "table"     => "array_table",
            "key"       => "owner_id",
            "mapKey"    => "name",
            "element"   => "value",
        ));

        $this->persister = new Zend_Db_Mapper_Persister_Array($arrayDef);
    }

    public function testAddedElementsAreInserted()
    {
        $ownerId = 1;

        $arrayObject = new Zend_Entity_Collection_Array();
        $arrayObject['foo'] = 'bar';
        $arrayObject['bar'] = 'baz';

        $this->persister->persist($ownerId, $arrayObject, $this->entityManager);

        $queries = $this->testAdapter->getProfiler()->getQueryProfiles();
        $this->assertEquals(2, count($queries));
        $this->assertEquals('INSERT INTO array_table (owner_id, name, value) VALUES (?, ?, ?)', $queries[0]->getQuery());
        $this->assertEquals('INSERT INTO array_table (owner_id, name, value) VALUES (?, ?, ?)', $queries[1]->getQuery());
        $this->assertEquals(array(1 => 1, 2 => 'foo', 3 => 'bar'), $queries[0]->getQueryParams());
        $this->assertEquals(array(1 => 1, 2 => 'bar', 3 => 'baz'), $queries[1]->getQueryParams());
    }

    public function testRemovedElementsAreDeleted()
    {
        $ownerId = 1;

        $data = array('foo' => 'bar', 'bar' => 'baz');
        $arrayObject = new Zend_Entity_Collection_Array($data);
        unset($arrayObject['foo']);
        unset($arrayObject['bar']);

        $this->persister->persist($ownerId, $arrayObject, $this->entityManager);

        $queries = $this->testAdapter->getProfiler()->getQueryProfiles();
        $this->assertEquals(2, count($queries));
        $this->assertEquals('DELETE FROM array_table WHERE (owner_id = 1 AND name = \'foo\')', $queries[0]->getQuery());
        $this->assertEquals('DELETE FROM array_table WHERE (owner_id = 1 AND name = \'bar\')', $queries[1]->getQuery());
    }

    public function testRemoveSpecificItemAndReAdd_FirstDeleteThenInsert_ForConsistency()
    {
        $ownerId = 1;

        $data = array('foo' => 'bar');
        $arrayObject = new Zend_Entity_Collection_Array($data);
        unset($arrayObject['foo']);
        $arrayObject['foo'] = "baz";

        $this->persister->persist($ownerId, $arrayObject, $this->entityManager);

        $queries = $this->testAdapter->getProfiler()->getQueryProfiles();
        $this->assertEquals(2, count($queries));
        $this->assertEquals('DELETE FROM array_table WHERE (owner_id = 1 AND name = \'foo\')', $queries[0]->getQuery());
        $this->assertEquals('INSERT INTO array_table (owner_id, name, value) VALUES (?, ?, ?)', $queries[1]->getQuery());
        $this->assertEquals(array(1 => $ownerId, 2 => 'foo', 3 => 'baz'), $queries[1]->getQueryParams());
    }

    public function testUpdateElement_SyncWithDb()
    {
        $ownerId = 1;

        $data = array('foo' => 'bar');
        $arrayObject = new Zend_Entity_Collection_Array($data);
        $arrayObject['foo'] = 'baz';

        $this->persister->persist($ownerId, $arrayObject, $this->entityManager);

        $queries = $this->testAdapter->getProfiler()->getQueryProfiles();
        $this->assertEquals(2, count($queries));
        $this->assertEquals('DELETE FROM array_table WHERE (owner_id = 1 AND name = \'foo\')', $queries[0]->getQuery());
        $this->assertEquals('INSERT INTO array_table (owner_id, name, value) VALUES (?, ?, ?)', $queries[1]->getQuery());
        $this->assertEquals(array(1 => $ownerId, 2 => 'foo', 3 => 'baz'), $queries[1]->getQueryParams());
    }
}