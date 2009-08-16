<?php

class Zend_Entity_Mapper_Loader_Entity_ManyToOneFixtureTest extends Zend_Entity_Mapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Entity_Mapper_Loader_Entity";
    }

    public function setUp()
    {
        parent::setUp();
        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $this->resourceMap = $this->fixture->getResourceMap();
    }

    public function testLoadRowCreatesLazyLoadEntity()
    {
        $entity = $this->doLoadManyToOneFixtureRowEntity();

        $state = $entity->getState();
        $this->assertTrue(isset($state[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]));
        $this->assertLazyLoad($state[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]);
    }

    public function testConsecutiveLoadRowCreateIdentityMappedLazyLoadEntities()
    {
        $entity1 = $this->doLoadManyToOneFixtureRowEntity(1);
        $entity2 = $this->doLoadManyToOneFixtureRowEntity(2);

        $state1 = $entity1->getState();
        $state2 = $entity2->getState();
        $this->assertTrue(isset($state1[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]));
        $this->assertTrue(isset($state2[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]));
        $this->assertSame($state1[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE], $state2[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]);
    }

    public function testLoadRowCreatesloadCallbackInLazyLoadObject()
    {
        $entity = $this->doLoadManyToOneFixtureRowEntity();

        $state = $entity->getState();
        $lazyLoadCollection = $state[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE];

        $lazyLoadArgs = $this->readAttribute($lazyLoadCollection, '_callbackArguments');
        $this->assertEquals(Zend_Entity_Fixture_ManyToOneDefs::TEST_B_CLASS, $lazyLoadArgs[0]);
        $this->assertEquals(Zend_Entity_Fixture_ManyToOneDefs::DUMMY_DATA_MANYTOONE, $lazyLoadArgs[1]);

        $lazyLoadCallback = $this->readAttribute($lazyLoadCollection, '_callback');
        $this->assertTrue($lazyLoadCallback[0] instanceof Zend_Entity_Manager_Interface);
        $this->assertEquals("load", $lazyLoadCallback[1]);
    }

    public function testLoadRowWithLateBoundFetching()
    {
        $entityManager = $this->createEntityManager();

        $relatedFetchStmtResult = new Zend_Entity_DbStatementMock();
        $relatedFetchStmtResult->appendToFetchStack($this->fixture->getDummyDataRowClassB());
        $entityManager->getAdapter()->appendStatementToStack($relatedFetchStmtResult);

        $entityDefinition = $this->resourceMap->getDefinitionByEntityName(Zend_Entity_Fixture_ManyToOneDefs::TEST_A_CLASS);
        $entityDefinition->getPropertyByName(Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE)->setFetch("select");

        $entity = $this->doLoadManyToOneFixtureRowEntity();
        $entityState = $entity->getState();

        $this->assertNotEquals(1, $entityState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]);
        $this->assertTrue($entityState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE] instanceof Zend_TestEntity2);
        $this->assertEquals($this->fixture->getDummyDataStateClassB(), $entityState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]->getState());
    }

    protected function doLoadManyToOneFixtureRowEntity()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->fixture->getClassALoader();
        $row = $this->fixture->getDummyDataRowClassA();

        if($this->entityManager !== null) {
            $entityManager = $this->entityManager;
        } else {
            $entityManager = $this->createEntityManager();
        }

        $loader->loadRow($entity, $row, $entityManager);
        return $entity;
    }
}