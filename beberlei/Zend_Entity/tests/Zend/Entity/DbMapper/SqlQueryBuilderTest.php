<?php

class Zend_Entity_DbMapper_SqlQueryBuilderTest extends Zend_Entity_TestCase
{
    public function testWith_ForInvalidEntityName_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $em = $this->createEntityManager();
        $qo = new Zend_Db_Mapper_QueryObject(new Zend_Test_DbAdapter());

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->with("UnknownEntityName");
    }

    public function testWith_AddsColumnsToResultSetMap_ByUsingMappings()
    {
        $fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
        $em = $fixture->createTestEntityManager();

        $qo = new Zend_Db_Mapper_QueryObject(new Zend_Test_DbAdapter());

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->from('table')->with('Zend_TestEntity1');

        $rsm = $qb->getResultSetMapping();

        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertEquals(
            array('a_id' => 'id', 'a_property' => 'property'),
            $rsm->entityResult['Zend_TestEntity1']['properties']
        );
    }

    public function testWith_AddsColumnsToQueryObject_ByUsingMappings()
    {
        $fixtureTable = "table";

        $fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('from')
           ->with($this->equalTo($fixtureTable));
        $qo->expects($this->at(1))
            ->method('columns')
            ->with($this->equalTo(array('a_id' => 'a_id', 'a_property' => 'a_property')));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->from('table')->with('Zend_TestEntity1');
    }

    public function testWithForJoinTable_UseCorrelationToRefer_CreatesTwoRootEntitiesInResultSetMap()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = new Zend_Db_Mapper_QueryObject(new Zend_Test_DbAdapter());

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->from('table_a')->with('Zend_TestEntity1')
           ->join('table_b', 'table_a.manytoone = table_b.id')->with('Zend_TestEntity2', 'table_b');

        $rsm = $qb->getResultSetMapping();

        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity2']));
        $this->assertEquals(array('Zend_TestEntity1', 'Zend_TestEntity2'), $rsm->rootEntity);
    }

    public function testJoinWith_AddAsJoinedResult()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = new Zend_Db_Mapper_QueryObject(new Zend_Test_DbAdapter());

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->from('table_a')->with('Zend_TestEntity1')
           ->joinWith('table_b', 'table_a.manytoone = table_b.id', 'Zend_TestEntity2');

        $rsm = $qb->getResultSetMapping();

        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity2']));
        $this->assertEquals(array('Zend_TestEntity1'), $rsm->rootEntity);
        $this->assertEquals(
            array('Zend_TestEntity2' => array('parentEntity' => '', 'parentProperty' => '')),
            $rsm->joinedEntity
        );
    }

    public function testJoinWith_UseArrayCorrelationNameTableReference()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = new Zend_Db_Mapper_QueryObject(new Zend_Test_DbAdapter());

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->from('table_a')->with('Zend_TestEntity1')
           ->joinWith(array('b' => 'table_b'), 'table_a.manytoone = b.id', 'Zend_TestEntity2');

        $rsm = $qb->getResultSetMapping();

        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity2']));
        $this->assertEquals(array('Zend_TestEntity1'), $rsm->rootEntity);
        $this->assertEquals(
            array('Zend_TestEntity2' => array('parentEntity' => '', 'parentProperty' => '')),
            $rsm->joinedEntity
        );
    }

    public function testJoinWith_UseArrayNumericTableReference()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = new Zend_Db_Mapper_QueryObject(new Zend_Test_DbAdapter());

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->from('table_a')->with('Zend_TestEntity1')
           ->joinWith(array('table_b'), 'table_a.manytoone = table_b.id', 'Zend_TestEntity2');

        $rsm = $qb->getResultSetMapping();

        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity2']));
        $this->assertEquals(array('Zend_TestEntity1'), $rsm->rootEntity);
        $this->assertEquals(
            array('Zend_TestEntity2' => array('parentEntity' => '', 'parentProperty' => '')),
            $rsm->joinedEntity
        );
    }

    /**
     *
     * @param <type> $queryObject
     * @param <type> $loader
     * @param <type> $em
     * @return Zend_Db_Mapper_NativeQuery 
     */
    public function createNativeQueryBuilder($queryObject, $loader, $em)
    {
        $mapper = $this->getMock('Zend_Entity_TestMapper', array(), array(), '', false);
        $mapper->expects($this->any())
               ->method('getLoader')
               ->will($this->returnValue($loader));
        $em->setMapper($mapper);
        
        return new Zend_Db_Mapper_SqlQueryBuilder($em, $queryObject);
    }

    public function testGetResultList_DelegatesToLoader_ProcessResultset()
    {
        $fixtureReturnValue = "foo";

        $em = $this->createTestingEntityManager();
        $select = $this->createQueryObjectMock();
        $loader = $this->getLoaderMock($select);
        $this->addProcessResultsetExpectation($loader, $fixtureReturnValue, $em);

        $query = $this->createNativeQueryBuilder($select, $loader, $em);
        $result = $query->getResultList();

        $this->assertEquals($fixtureReturnValue, $result);
    }

    public function addProcessResultsetExpectation($loader, $returnValue, $em)
    {
        $loader->expects($this->once())
               ->method('processResultset')
               ->with($this->isType('array'), $this->isInstanceOf('Zend_Entity_Query_ResultSetMapping'))
               ->will($this->returnValue($returnValue));
    }

    public function testGetSingleResult_ReturnValue_IfOneResultOnly()
    {
        $fixtureReturnValue = array( array("foo" => "bar") );

        $query = $this->createQueryWithResultExpectation($fixtureReturnValue);
        $result = $query->getSingleResult();

        $this->assertEquals(
            array("foo" => "bar"), $result
        );
    }

    public function testGetSingleResult_ThrowException_WhenMoreThanOneResult()
    {
        $this->setExpectedException("Zend_Entity_NonUniqueResultException");

        $fixtureReturnValue = array( array("foo" => "bar"), array("foo" => "baz") );

        $query = $this->createQueryWithResultExpectation($fixtureReturnValue);
        $query->getSingleResult();
    }

    public function testGetSingleResult_ThrowException_WhenNoResult()
    {
        $this->setExpectedException("Zend_Entity_NoResultException");

        $fixtureReturnValue = array();

        $query = $this->createQueryWithResultExpectation($fixtureReturnValue);
        $query->getSingleResult();
    }

    public function testGetSingleResult_ReturnNull_WhenNoResult_AndHintIsSet()
    {
        $fixtureReturnValue = array();

        $query = $this->createQueryWithResultExpectation($fixtureReturnValue);
        $query->setHint("singleResultNotFound", Zend_Entity_Manager::NOTFOUND_NULL);
        $this->assertNull($query->getSingleResult());
    }

    public function createQueryWithResultExpectation($fixtureReturnValue)
    {
        $em = $this->createTestingEntityManager();
        $select = $this->createQueryObjectMock();
        $loader = $this->getLoaderMock($select);
        $this->addProcessResultsetExpectation($loader, $fixtureReturnValue, $em);

        return $this->createNativeQueryBuilder($select, $loader, $em);
    }

    public function testSetMaxResults()
    {
        $select = $this->createQueryObjectMock();
        $select->expects($this->once())
               ->method('limit')
               ->with(30, null);

        $query = $this->createDbSelectQuery($select);
        $q = $query->setMaxResults(30);

        $this->assertSame($query, $q);
    }

    public function testSetFirstResult()
    {
        $select = $this->createQueryObjectMock();
        $select->expects($this->once())
               ->method('limit')
               ->with(null, 30);

        $query = $this->createDbSelectQuery($select);
        $q = $query->setFirstResult(30);

        $this->assertSame($query, $q);
    }

    public function testSetMaxAndFirstResult()
    {
        $select = $this->createQueryObjectMock();
        $select->expects($this->at(0))
               ->method('limit')
               ->with(30, null);
        $select->expects($this->at(1))
               ->method('limit')
               ->with(30, 30);

        $query = $this->createDbSelectQuery($select);
        $query->setMaxResults(30)->setFirstResult(30);
    }

    public function testGetDefaultParamaterValue_IsNull()
    {
        $query = $this->createDbSelectQuery();
        $this->assertNull($query->getParam('foo'));
    }

    public function testSetParameter()
    {
        $query = $this->createDbSelectQuery();
        $query->bindParam('foo', 'bar');

        $this->assertEquals('bar', $query->getParam('foo'));
        $this->assertEquals(array('foo' => 'bar'), $query->getParams());
    }

    public function testSetParameters()
    {
        $query = $this->createDbSelectQuery();
        $query->bindParams(array('foo' => 'bar', 'bar' => 'baz'));

        $this->assertEquals('bar', $query->getParam('foo'));
        $this->assertEquals('baz', $query->getParam('bar'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $query->getParams());
    }

    public function testSetParametersDoesNotResetBindings()
    {
        $query = $this->createDbSelectQuery();
        $query->bindParam('baz', 'foo');
        $query->bindParams(array('foo' => 'bar', 'bar' => 'baz'));

        $this->assertEquals(array('baz' => 'foo', 'foo' => 'bar', 'bar' => 'baz'), $query->getParams());
    }

    public function testGetUnknownHint()
    {
        $query = $this->createDbSelectQuery();

        $this->assertFalse($query->getHint("foo"));
    }

    public function testSetGetHint()
    {
        $query = $this->createDbSelectQuery();
        $query->setHint("foo", "bar");

        $this->assertEquals("bar", $query->getHint("foo"));
    }

    public function testSetHint_IsFluent()
    {
        $query = $this->createDbSelectQuery();
        $q = $query->setHint("foo", "bar");

        $this->assertSame($q, $query);
    }

    public function createDbSelectQuery($select=null)
    {
        if($select == null) {
            $select = $this->createQueryObjectMock();
        }

        $loader = $this->getLoaderMock($select);
        $em = $this->createTestingEntityManager();

        return $this->createNativeQueryBuilder($select, $loader, $em);
    }

    public function getLoaderMock()
    {
        return $this->getMock('Zend_Db_Mapper_Loader_LoaderAbstract', array(), array(), '', false);
    }

    public function createQueryObjectMock()
    {
        $select = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $select->expects($this->any())
               ->method('query')
               ->will($this->returnValue(new Zend_Test_DbStatement()));
        return $select;
    }
}