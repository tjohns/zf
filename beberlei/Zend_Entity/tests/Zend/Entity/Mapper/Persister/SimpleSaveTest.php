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

        $identityMapMock = $this->createIdentityMapMock(0);
        $identityMapMock->expects($this->once())->method('contains')->will($this->returnValue(true));
        $identityMapMock->expects($this->exactly(2))->method('getPrimaryKey')->will($this->returnValue(1));

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('update')
           ->with('entities', $columnExpectedState, $columnExpectedWhere);
        $db->expects($this->once())
           ->method('quoteInto')
           ->with('entities.entities_id = ?', 1)
           ->will($this->returnValue('entities.entities_id = 1'));

        $em = $this->createTestingEntityManager(null, null, $identityMapMock, $db);

        $persister = $this->createPersister();
        $persister->doPerformSave($entity, $columnFullState, $em);
    }

    public function testInsertNewEntity_UpdatesIdentityMap()
    {
        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $newId = 1;
        $columnExpectedState = array('entities_id' => null, 'foo' => 'foo', 'baz' => 'bar');

        $entity = new Zend_TestEntity1;

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())->method('lastInsertId')->will($this->returnValue($newId));

        $identityMap = $this->createIdentityMapMock(0);
        $identityMap->expects($this->once())
                    ->method('addObject')
                    ->with('Zend_TestEntity1', $newId, $entity);

        $em = $this->createTestingEntityManager(null, null, $identityMap, $db);

        $persister = $this->createPersister();
        $persister->doPerformSave($entity, $columnExpectedState, $em);
    }

    public function testSaveEntity_WithRelatedEntity_SavesForeignKey_IntoDb()
    {
        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $entityA = new Zend_TestEntity1();
        $entityA->setState($this->fixture->getDummyDataStateClassA());
        $entityB = new Zend_TestEntity2();
        $entityB->setState($this->fixture->getDummyDataStateClassB());
        $entityA->setmanytoone($entityB);

        $persister = $this->createPersister();
        $actualDbState = $persister->transformEntityToDbState($entityA->getState(), $this->createTestingEntityManager());

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
        $entityB = new Zend_Entity_Mapper_LazyLoad_Entity('trim', array('load', $relatedId));
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

        $em = $this->getMock('Zend_Entity_Manager_Interface');
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

        $collection = new Zend_Entity_Mapper_LazyLoad_Collection('trim', array(1, 2));

        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $em->expects($this->exactly(0))->method('save');

        $persister = $this->createPersister();
        $persister->evaluateRelatedCollection("1", $collection, $collectionDef, $em);
    }
}