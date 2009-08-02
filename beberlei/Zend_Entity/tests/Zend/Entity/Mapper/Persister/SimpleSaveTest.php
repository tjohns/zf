<?php

require_once dirname(__FILE__)."/../../../../TestHelper.php";

class Zend_Entity_Mapper_Persister_SimpleSaveTest extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_Fixture_Abstract
     */
    private $fixture = null;

    /**
     *
     * @return Zend_Entity_Mapper_Persister_Simple
     */
    public function createPersister()
    {
        if($this->fixture == null) {
            throw new Exception("createPersister() requires a \$this->fixture to be set with a fixture object.");
        }

        $entityDef = $this->fixture->getEntityDefinition('Zend_TestEntity1');
        $defMap = $this->fixture->getResourceMap();

        $persister = new Zend_Entity_Mapper_Persister_Simple();
        $persister->initialize($entityDef, $defMap);

        return $persister;
    }

    public function createEntity($initialState)
    {
        $entity = new Zend_TestEntity1();
        $entity->setState( $initialState );
        return $entity;
    }

    public function testSaveEntity_WithoutPrimaryKey_InsertsDbState_IntoDbAdapter()
    {
        $this->fixture = new Zend_Entity_Fixture_RelationLessDefs();

        $newId = 1;

        $columnExpectedState = array('entities_id' => null, 'foo' => 'foo', 'baz' => 'bar');

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('insert')
           ->with('entities', $columnExpectedState);
        $db->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue($newId));

        $em = $this->createTestingEntityManager(null, null, null, $db);

        $entity = new Zend_TestEntity1();

        $persister = $this->createPersister();
        $persister->doPerformSave($entity, $columnExpectedState, $em);

        $entityState = $entity->getState();
        $this->assertEquals($newId, $entityState['id']);
    }

    public function testSaveEntity_WithPrimaryKey_UpdatesDbState_WithWhereCondition()
    {
        $this->fixture = new Zend_Entity_Fixture_RelationLessDefs();

        $columnExpectedState = array('foo' => 'foo', 'baz' => 'bar');
        $columnFullState = array_merge($columnExpectedState, array('entities_id' => 1));
        $columnExpectedWhere = 'entities.entities_id = 1';

        $entity = new Zend_TestEntity1;

        $identityMap = new Zend_Entity_IdentityMap();
        $identityMap->addObject("Zend_TestEntity1", "1", $entity);

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('update')
           ->with('entities', $columnExpectedState, $columnExpectedWhere);
        $db->expects($this->once())
           ->method('quoteInto')
           ->with('entities.entities_id = ?', 1)
           ->will($this->returnValue('entities.entities_id = 1'));

        $em = $this->createTestingEntityManager(null, null, $identityMap, $db);

        $persister = $this->createPersister();
        $persister->doPerformSave($entity, $columnFullState, $em);
    }

    public function testInsertNewEntity_UpdatesIdentityMap()
    {
        $this->fixture = new Zend_Entity_Fixture_RelationLessDefs();
        
        $newId = 1;
        $columnExpectedState = array('entities_id' => null, 'foo' => 'foo', 'baz' => 'bar');

        $entity = new Zend_TestEntity1;

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())->method('lastInsertId')->will($this->returnValue($newId));

        $em = $this->createTestingEntityManager(null, null, null, $db);

        $persister = $this->createPersister();

        $this->assertFalse($em->getIdentityMap()->hasObject("Zend_TestEntity1", $newId));
        $persister->doPerformSave($entity, $columnExpectedState, $em);
        $this->assertTrue($em->getIdentityMap()->hasObject("Zend_TestEntity1", $newId));
    }

    public function testInsertNewEntity_WithVersion_SetsVersionField()
    {
        $this->fixture = new Zend_Entity_Fixture_RelationLessDefs();
        $def = $this->fixture->getEntityDefinition("Zend_TestEntity1");
        $def->addVersion("version", array("columnName" => "version"));

        $newId = 1;
        $columnSaveState = array('entities_id' => null, 'foo' => 'foo', 'baz' => 'bar', 'version' => null);
        $columnExpectedState = array('entities_id' => null, 'foo' => 'foo', 'baz' => 'bar', 'version' => 1);

        $entity = new Zend_TestEntity1;

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())->method('lastInsertId')->will($this->returnValue($newId));
        $db->expects($this->once())->method('insert')->with($this->equalTo('entities'), $this->equalTo($columnExpectedState));

        $em = $this->createTestingEntityManager(null, null, null, $db);

        $persister = $this->createPersister();

        $this->assertFalse($em->getIdentityMap()->getVersion($entity));
        $persister->doPerformSave($entity, $columnExpectedState, $em);
        $this->assertEquals(1, $em->getIdentityMap()->getVersion($entity));
    }

    public function testUpdateEntity_WithVersion_IncrementsVersionId()
    {
        $this->doUpdateEntity_WithVersion_AffectedRows(1);
    }

    public function testUpdateEntity_WithVersion_OptimisticLockException()
    {
        $this->setExpectedException("Zend_Entity_OptimisticLockException");

        $this->doUpdateEntity_WithVersion_AffectedRows(0);
    }

    public function doUpdateEntity_WithVersion_AffectedRows($affectedRows)
    {
        $this->fixture = new Zend_Entity_Fixture_RelationLessDefs();
        $def = $this->fixture->getEntityDefinition("Zend_TestEntity1");
        $def->addVersion("version", array("columnName" => "version"));

        $entity = new Zend_TestEntity1;
        $fixtureVersionId = 1;
        $fixtureId = 1;

        $columnSaveState = array('entities_id' => $fixtureId, 'foo' => 'foo', 'baz' => 'bar', 'version' => $fixtureVersionId);
        $columnExpectedUpdateState = array('foo' => 'foo', 'baz' => 'bar', 'version' => $fixtureVersionId+1);
        $expectedUpdateWhereCondition = 'entities.entities_id = 1 AND entities.version = 1';

        $identityMap = new Zend_Entity_IdentityMap();
        $identityMap->addObject("Zend_TestEntity1", $fixtureId, $entity, $fixtureVersionId);

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->at(0))
           ->method('quoteInto')
           ->will($this->returnValue("entities.entities_id = 1"));
        $db->expects($this->at(1))
           ->method('quoteInto')
           ->with($this->equalTo('entities.version = ?'), $this->equalTo($fixtureVersionId))
           ->will($this->returnValue('entities.version = 1'));
        $db->expects($this->at(2))
           ->method('update')
           ->with($this->equalTo('entities'), $this->equalTo($columnExpectedUpdateState), $this->equalTo($expectedUpdateWhereCondition))
           ->will($this->returnValue($affectedRows));

        $em = $this->createEntityManager(null, null, $identityMap, $db);
        $persister = $this->createPersister();

        $persister->doPerformSave($entity, $columnSaveState, $em);

        $this->assertEquals($fixtureVersionId+1, $identityMap->getVersion($entity));
    }

    public function testSaveEntity_WithRelatedEntity_SavesForeignKey_IntoDb()
    {
        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $entityA = new Zend_TestEntity1();
        $entityA->setState($this->fixture->getDummyDataStateClassA());
        $entityB = new Zend_TestEntity2();
        $entityB->setState($this->fixture->getDummyDataStateClassB());
        $entityA->setmanytoone($entityB);

        $identityMap = new Zend_Entity_IdentityMap();
        $identityMap->addObject("Zend_TestEntity2", "1", $entityB);

        $em = $this->createTestingEntityManager(null, null, $identityMap);

        $persister = $this->createPersister();
        $actualDbState = $persister->transformEntityToDbState($entityA->getState(), $em);

        $this->assertEquals(
            $entityB->getid(),
            $actualDbState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE_COLUMN]
        );
    }

    public function testSaveEntity_WithRelatedLazyEntity_SavesForeignKey_IntoDb()
    {
        $relatedId = 1;

        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $entity = new Zend_TestEntity1();
        $entity->setState($this->fixture->getDummyDataStateClassA());
        $entityB = new Zend_Entity_LazyLoad_Entity('trim', array('load', $relatedId));
        $entity->setmanytoone($entityB);

        $persister = $this->createPersister();
        $actualDbState = $persister->transformEntityToDbState($entity->getState(), $this->createTestingEntityManager());

        $this->assertEquals(
            $relatedId,
            $actualDbState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE_COLUMN]
        );
    }

    public function testSaveEntity_WithRelatedNull_SavesNull_IntoDb()
    {
        $emptyRelation = null;

        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $entity = new Zend_TestEntity1();
        $entity->setState($this->fixture->getDummyDataStateClassA());
        $entity->setmanytoone($emptyRelation);

        $persister = $this->createPersister();
        $actualDbState = $persister->transformEntityToDbState($entity->getState(), $this->createTestingEntityManager());

        $this->assertEquals(
            $emptyRelation,
            $actualDbState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE_COLUMN]
        );
    }


    public function testSaveEntity_WithPersistCascadeSave_DelegatesToEntityManager()
    {
        $this->doTestSaveEntityWithCascade("save");
    }

    public function testSaveEntity_WithPersistCascadeAll_DelegatesToEntityManager()
    {
        $this->doTestSaveEntityWithCascade("all");
    }

    public function testSaveEntity_WithPersistCascadeDeleteAndNone()
    {
        $this->doTestSaveEntityWithCascade("delete", false);
        $this->doTestSaveEntityWithCascade("none", false);
    }

    protected function doTestSaveEntityWithCascade($cascade, $delegates=true)
    {
        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        
        $relationDef = $this->fixture->getEntityPropertyDef('Zend_TestEntity1', 'manytoone');
        $relationDef->setCascade($cascade);

        $entityB = new Zend_TestEntity2();
        $entityB->setState($this->fixture->getDummyDataStateClassB());

        // Make sure $identityMap->getPrimaryKey() returns a value on this $entityB
        $identityMap = new Zend_Entity_IdentityMap();
        $identityMap->addObject("Zend_TestEntity2", "1", $entityB);

        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $em->expects($this->once())
           ->method('getIdentityMap')
           ->will($this->returnValue($identityMap));
        if($delegates == true) {
            $em->expects($this->once())
               ->method('save')
               ->with($entityB);
        } else {
            $em->expects($this->never())->method('save');
        }

        $persister = $this->createPersister();
        $persister->evaluateRelatedObject($entityB, $relationDef, $em);
    }

    public function testSaveEntity_WithRelatedCascading_OneToManyCollection()
    {
        $this->fixture = new Zend_Entity_Fixture_OneToManyDefs();

        $collectionDef = $this->fixture->getEntityPropertyDef('Zend_TestEntity1', 'onetomany');
        $collectionDef->getRelation()->setCascade("save");

        $entityA = new Zend_TestEntity2();
        $entityB = new Zend_TestEntity2();
        $collection = new Zend_Entity_Collection(array($entityA, $entityB), 'Zend_TestEntity2');

        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $em->expects($this->exactly(2))->method('save');

        $persister = $this->createPersister();
        $persister->evaluateRelatedCollection("1", $collection, $collectionDef, $em);
    }

    public function testSaveEntity_WithRelatedCascading_LazyLoadCollection()
    {
        $this->fixture = new Zend_Entity_Fixture_OneToManyDefs();

        $collectionDef = $this->fixture->getEntityPropertyDef('Zend_TestEntity1', 'onetomany');
        $collectionDef->getRelation()->setCascade("save");

        $collection = new Zend_Entity_LazyLoad_Collection('trim', array(1, 2));

        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $em->expects($this->exactly(0))->method('save');

        $persister = $this->createPersister();
        $persister->evaluateRelatedCollection("1", $collection, $collectionDef, $em);
    }

    public function testDeleteEntity()
    {
        $this->fixture = new Zend_Entity_Fixture_RelationLessDefs();

        $fixtureId = 1;
        $fixtureTable = "entities";
        $entity = new Zend_TestEntity1();

        $identityMap = new Zend_Entity_IdentityMap();
        $identityMap->addObject("Zend_TestEntity1", $fixtureId, $entity);

        $dbMock = $this->createDatabaseConnectionMock();
        $dbMock->expects($this->at(0))
               ->method('quoteInto')
               ->will($this->returnValue("foo"));
        $dbMock->expects($this->at(1))
               ->method('delete')
               ->with($this->equalTo($fixtureTable));

        $em = $this->createTestingEntityManager(null, null, $identityMap, $dbMock);

        $persister = $this->createPersister();
        $persister->delete($entity, $em);
    }

    public function testDeleteEntity_WithVersion()
    {
        $this->doDeleteEntity_WithVersion_ReturnsAffectedRows(1);
    }

    public function testDeleteEntity_WithVersion_OptimisticLockException()
    {
        $this->setExpectedException("Zend_Entity_OptimisticLockException");

        $this->doDeleteEntity_WithVersion_ReturnsAffectedRows(0);
    }

    public function doDeleteEntity_WithVersion_ReturnsAffectedRows($affectedRows)
    {
        $this->fixture = new Zend_Entity_Fixture_RelationLessDefs();
        $def = $this->fixture->getEntityDefinition('Zend_TestEntity1');
        $def->addVersion("version", array("columnName" => "version"));

        $fixtureId = 1;
        $fixtureVersionId = 1;
        $fixtureTable = "entities";
        $entity = new Zend_TestEntity1();

        $identityMap = new Zend_Entity_IdentityMap();
        $identityMap->addObject("Zend_TestEntity1", $fixtureId, $entity, $fixtureVersionId);

        $dbMock = $this->createDatabaseConnectionMock();
        $dbMock->expects($this->at(0))
               ->method('quoteInto')
               ->will($this->returnValue('id = 1'));
        $dbMock->expects($this->at(1))
               ->method('quoteInto')
               ->with($this->equalTo("entities.version = ?"), $this->equalTo($fixtureVersionId))
               ->will($this->returnValue("entities.version = 1"));
        $dbMock->expects($this->at(2))
               ->method('delete')
               ->with($this->equalTo('entities'), $this->equalTo('id = 1 AND entities.version = 1'))
               ->will($this->returnValue($affectedRows));

        $em = $this->createTestingEntityManager(null, null, $identityMap, $dbMock);

        $persister = $this->createPersister();
        $persister->delete($entity, $em);
    }

    public function testUpdateCollections_ElementHashMap_DeleteRemovedElements()
    {
        $fixtureId = 1;
        $this->fixture = new Zend_Entity_Fixture_CollectionElementDefs();

        $elements = new Zend_Entity_Collection_ElementHashMap(array("foo" => "bar"));
        unset($elements["foo"]);
        $entityState = array('elements' => $elements);

        $dbMock = $this->createDatabaseConnectionMock();
        $dbMock->expects($this->at(0))
               ->method('quoteInto')
               ->with($this->equalTo('fk_id = ?'), $this->equalTo(1))
               ->will($this->returnValue('fk_id = 1'));
        $dbMock->expects($this->at(1))
               ->method('quoteInto')
               ->with($this->equalTo('col_key = ?'), $this->equalTo("foo"))
               ->will($this->returnValue("col_key = 'foo'"));
        $dbMock->expects($this->at(2))
               ->method('delete')
               ->with($this->equalTo('entities_elements'), $this->equalTo("fk_id = 1 AND col_key = 'foo'"));

        $em = $this->createTestingEntityManager(null, null, null, $dbMock);

        $persister = $this->createPersister();
        $persister->updateCollections($fixtureId, $entityState, $em);
    }

    public function testUpdateCollections_ElementHashMap_InsertAddedElements()
    {
        $fixtureId = 1;
        $this->fixture = new Zend_Entity_Fixture_CollectionElementDefs();

        $expectedInsertData = array(
            'fk_id' => $fixtureId,
            'col_key' => 'foo',
            'col_name' => 'bar'
        );

        $elements = new Zend_Entity_Collection_ElementHashMap();
        $elements["foo"] = "bar";
        
        $entityState = array('elements' => $elements);

        $dbMock = $this->createDatabaseConnectionMock();
        $dbMock->expects($this->at(0))
               ->method('insert')
               ->with($this->equalTo('entities_elements'), $expectedInsertData);

        $em = $this->createTestingEntityManager(null, null, null, $dbMock);

        $persister = $this->createPersister();
        $persister->updateCollections($fixtureId, $entityState, $em);
    }

    public function testUpdateCollections_NoElementHashMap_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $fixtureId = 1;
        $this->fixture = new Zend_Entity_Fixture_CollectionElementDefs();

        $entityState = array('elements' => null);

        $em = $this->createTestingEntityManager();

        $persister = $this->createPersister();
        $persister->updateCollections($fixtureId, $entityState, $em);
    }
}