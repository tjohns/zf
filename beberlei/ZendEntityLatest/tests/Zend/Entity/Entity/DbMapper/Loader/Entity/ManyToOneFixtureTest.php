<?php

class Zend_Entity_DbMapper_Loader_Entity_ManyToOneFixtureTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_ManyToOneDefs";
    }

    public function testLoadRowCreatesLazyLoadEntity()
    {
        $entity = $this->doLoadManyToOneFixtureRowEntity();

        $state = $entity->getState();
        $this->assertTrue(isset($state[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]));
        $this->assertLazyLoad($state[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE]);
    }

    public function testLoadRow_StoreToManyObject_AsRelatedObject_InIdentityMap()
    {
        $entity = $this->doLoadManyToOneFixtureRowEntity();

        $state = $entity->getState();
        $manyObject = $state[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE];
        $this->assertSame($manyObject, $this->identityMap->getRelatedObject($entity, Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE));
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

    public function testLoadRowWithLateBoundFetching()
    {
        $this->initFixture();

        $relatedFetchStmtResult = new Zend_Test_DbStatement();
        $relatedFetchStmtResult->append($this->fixture->getDummyDataRowClassB());
        $this->adapter->appendStatementToStack($relatedFetchStmtResult);

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
        $loader = $this->createLoader();
        $state = $this->fixture->getDummyDataStateClassA();

        return $loader->createEntityFromState($state, $this->mappings["Zend_TestEntity1"]);
    }

    public function testProcessResultSet_RootEntityOnlyResultSetMapping()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property')
            ->addProperty('a', 'a_manytoone', 'manytoone');

        $rows = array(
            array(
                'a_id' => 1,
                'a_property' => 'foo',
                'a_manytoone' => 1,
            )
        );

        $loader = $this->createLoader();
        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(1, count($result));
        $this->assertType('Zend_TestEntity1', $result[0]);
        $this->assertEquals(1, $result[0]->id);
        $this->assertLazyLoad($result[0]->manytoone);
    }

    public function testProcessResultSet_JoinedEntityConstructed_AlongsideRootEntity()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property')
            ->addProperty('a', 'a_manytoone', 'manytoone');
        $rsm->addJoinedEntity('Zend_TestEntity2', 'b', null, null)
            ->addProperty('b', 'b_id', 'id')
            ->addProperty('b', 'b_property', 'property');

        $rows = array(
            array(
                'a_id' => 1,
                'a_property' => 'foo',
                'a_manytoone' => 1,
                'b_id' => 1,
                'b_property' => 'baz',
            ),
            array(
                'a_id' => 2,
                'a_property' => 'bar',
                'a_manytoone' => 1,
                'b_id' => 1,
                'b_property' => 'baz',
            )
        );

        $loader = $this->createLoader();
        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(2, count($result));
        $this->assertType('Zend_TestEntity1', $result[0]);
        $this->assertEquals(1, $result[0]->id);
        $this->assertNotType('Zend_Entity_LazyLoad_Entity', $result[0]->manytoone);
        $this->assertType('Zend_TestEntity2', $result[0]->manytoone);
        $this->assertType('Zend_TestEntity1', $result[1]);
        $this->assertEquals(2, $result[1]->id);
        $this->assertNotType('Zend_Entity_LazyLoad_Entity', $result[1]->manytoone);
        $this->assertType('Zend_TestEntity2', $result[1]->manytoone);
        $this->assertSame($result[0]->manytoone, $result[1]->manytoone);
    }

    public function testProcessResultSet_WithScalars()
    {
        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property')
            ->addProperty('a', 'a_manytoone', 'manytoone');
        $rsm->addScalar('foo')->addScalar('bar');

        $rows = array(
            array(
                'a_id' => 1,
                'a_property' => 'foo',
                'a_manytoone' => 1,
                'foo' => 'foo',
                'bar' => 'bar',
            ),
            array(
                'a_id' => 2,
                'a_property' => 'bar',
                'a_manytoone' => 1,
                'foo' => 'foo',
                'bar' => 'bar',
            )
        );

        $relatedEntity = new Zend_TestEntity2();
        $this->identityMap->addObject('Zend_TestEntity2', 1, $relatedEntity);

        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(2, count($result));
        $this->assertType('Zend_TestEntity1', $result[0][0]);
        $this->assertEquals(1, $result[0][0]->id);
        $this->assertSame($relatedEntity, $result[0][0]->manytoone);
        $this->assertType('Zend_TestEntity1', $result[1][0]);
        $this->assertEquals(2, $result[1][0]->id);
        $this->assertSame($relatedEntity, $result[1][0]->manytoone);

        $this->assertTrue(isset($result[0]['foo']));
        $this->assertTrue(isset($result[0]['bar']));
        $this->assertTrue(isset($result[1]['foo']));
        $this->assertTrue(isset($result[1]['bar']));
    }

    public function testProcessResultSet_NoRootEntityInResultSetMap_ThrowsException()
    {
        $this->setExpectedException('Zend_Entity_Exception');

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rows = array();

        $loader = $this->createLoader();
        $result = $loader->processResultset($rows, $rsm);
    }

    /**
     * @group entityperformance
     */
    public function testPerformance_10000RootAndOneJoinedEntity_ShouldBeCreatedIn3Seconds()
    {
        $this->markTestSkipped();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property')
            ->addProperty('a', 'a_manytoone', 'manytoone');
        $rsm->addJoinedEntity('Zend_TestEntity2', 'b', null, null)
            ->addProperty('b', 'b_id', 'id')
            ->addProperty('b', 'b_property', 'property');

        $rows = array();

        for($i = 0; $i < 10000; $i++) {
            $rows[] = array('a_id' => $i, 'a_property' => 'foo', 'a_manytoone' => 1, 'b_id' => 1, 'b_property' => 'baz');
        }

        $loader = $this->createLoader();

        $this->startPerformanceMeasuring();

        $result = $loader->processResultset($rows, $rsm);

        $this->assertTookNoLongerThan(3);
        $this->assertEquals(10000, count($result));
    }

    /**
     * @group entityperformance
     */
    public function testPerformance_10000RootAndJoinedEntites_ShouldBeCreatedIn4Seconds()
    {
        $this->markTestSkipped();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property')
            ->addProperty('a', 'a_manytoone', 'manytoone');
        $rsm->addJoinedEntity('Zend_TestEntity2', 'b', null, null)
            ->addProperty('b', 'b_id', 'id')
            ->addProperty('b', 'b_property', 'property');

        $rows = array();

        for($i = 0; $i < 10000; $i++) {
            $rows[] = array('a_id' => $i, 'a_property' => 'foo', 'a_manytoone' => $i, 'b_id' => $i, 'b_property' => 'baz');
        }

        $loader = $this->createLoader();

        $this->startPerformanceMeasuring();

        $result = $loader->processResultset($rows, $rsm);

        $this->assertTookNoLongerThan(4);
        $this->assertEquals(10000, count($result));
    }
}