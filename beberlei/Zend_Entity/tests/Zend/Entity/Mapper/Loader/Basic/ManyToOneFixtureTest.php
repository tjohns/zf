<?php

class Zend_Entity_Mapper_Loader_Basic_ManyToOneFixtureTest extends Zend_Entity_Mapper_Loader_ManyToOneFixture
{
    public function createLoader($def)
    {
        return new Zend_Entity_Mapper_Loader_Basic($def);
    }

    public function testLoadRowCreatesLazyLoadEntity()
    {
        $entity = $this->doLoadManyToOneFixtureRowEntity();

        $state = $entity->getState();
        $this->assertTrue(isset($state[self::TEST_A_MANYTOONE]));
        $this->assertLazyLoad($state[self::TEST_A_MANYTOONE]);
    }

    public function testLoadRowCreatesFindByKeyCallbackInLazyLoadObject()
    {
        $entity = $this->doLoadManyToOneFixtureRowEntity();

        $state = $entity->getState();
        $lazyLoadCollection = $state[self::TEST_A_MANYTOONE];

        $lazyLoadArgs = $this->readAttribute($lazyLoadCollection, '_callbackArguments');
        $this->assertEquals(self::TEST_B_CLASS, $lazyLoadArgs[0]);
        $this->assertEquals(self::DUMMY_DATA_MANYTOONE, $lazyLoadArgs[1]);

        $lazyLoadCallback = $this->readAttribute($lazyLoadCollection, '_callback');
        $this->assertTrue($lazyLoadCallback[0] instanceof Zend_Entity_Manager_Interface);
        $this->assertEquals("findByKey", $lazyLoadCallback[1]);
    }

    public function testLoadRowWithLateBoundFetching()
    {
        $entityManager = $this->createEntityManager();

        $relatedFetchStmtResult = new Zend_Entity_DbStatementMock();
        $relatedFetchStmtResult->appendToFetchStack($this->getDummyDataRowClassB());
        $entityManager->getAdapter()->appendStatementToStack($relatedFetchStmtResult);

        $entityDefinition = $this->resourceMap->getDefinitionByEntityName(self::TEST_A_CLASS);
        $entityDefinition->getPropertyByName(self::TEST_A_MANYTOONE)->setFetch("select");

        $entity = $this->doLoadManyToOneFixtureRowEntity();
        $entityState = $entity->getState();

        $this->assertNotEquals(1, $entityState[self::TEST_A_MANYTOONE]);
        $this->assertTrue($entityState[self::TEST_A_MANYTOONE] instanceof Zend_TestEntity2);
        $this->assertEquals($this->getDummyDataStateClassB(), $entityState[self::TEST_A_MANYTOONE]->getState());
    }

    protected function doLoadManyToOneFixtureRowEntity()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->getClassALoader();
        $row = $this->getDummyDataRowClassA();

        if($this->entityManager !== null) {
            $entityManager = $this->entityManager;
        } else {
            $entityManager = $this->createEntityManager();
        }

        $loader->loadRow($entity, $row, $entityManager);
        return $entity;
    }
}