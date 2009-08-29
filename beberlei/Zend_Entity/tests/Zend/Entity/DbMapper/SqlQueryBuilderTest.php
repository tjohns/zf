<?php

class Zend_Entity_DbMapper_SqlQueryBuilderTest extends Zend_Entity_TestCase
{
    public function testWith_ForInvalidEntityName_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->with("UnknownEntityName");
    }

    public function testWith_AddsColumnsToResultSetMap_ByUsingMappings()
    {
        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->from('table')->with('Zend_TestEntity1');

        $rsm = $qb->getResultSetMapping();

        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertEquals(
            array('a_id' => 'id', 'a_property' => 'property', 'a_manytoone' => 'manytoone'),
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
        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->from('table_a')->with('Zend_TestEntity1')
           ->join('table_b', 'table_a.manytoone = table_b.id')->with('Zend_TestEntity2', 'table_b');

        $rsm = $qb->getResultSetMapping();

        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity2']));
        $this->assertEquals(array('Zend_TestEntity1', 'Zend_TestEntity2'), $rsm->rootEntity);
    }

    static public function dataJoinEntity()
    {
        return array(
            array('join', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a INNER JOIN table_b ON table_a.manytoone = table_b.id'),
            array('join', 'a', 'b', 'SELECT a.a_id, a.a_property, a.a_manytoone, b.b_id, b.b_property FROM table_a AS a INNER JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('join', null, 'b', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, b.b_id, b.b_property FROM table_a INNER JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('join', 'a', null, 'SELECT a.a_id, a.a_property, a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a AS a INNER JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinLeft', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a LEFT JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinLeft', 'a', 'b', 'SELECT a.a_id, a.a_property, a.a_manytoone, b.b_id, b.b_property FROM table_a AS a LEFT JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinLeft', null, 'b', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, b.b_id, b.b_property FROM table_a LEFT JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinLeft', 'a', null, 'SELECT a.a_id, a.a_property, a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a AS a LEFT JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinRight', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a RIGHT JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinRight', 'a', 'b', 'SELECT a.a_id, a.a_property, a.a_manytoone, b.b_id, b.b_property FROM table_a AS a RIGHT JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinRight', null, 'b', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, b.b_id, b.b_property FROM table_a RIGHT JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinRight', 'a', null, 'SELECT a.a_id, a.a_property, a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a AS a RIGHT JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinFull', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a FULL JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinFull', 'a', 'b', 'SELECT a.a_id, a.a_property, a.a_manytoone, b.b_id, b.b_property FROM table_a AS a FULL JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinFull', null, 'b', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, b.b_id, b.b_property FROM table_a FULL JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinFull', 'a', null, 'SELECT a.a_id, a.a_property, a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a AS a FULL JOIN table_b ON table_a.manytoone = table_b.id'),
        );
    }

    /**
     * @dataProvider dataJoinEntity
     */
    public function testJoinEntity($fn, $rootCorrelationName, $joinedCorrelationName, $expectedSql)
    {
        $entityFn = $fn."Entity";

        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->fromEntity('Zend_TestEntity1', $rootCorrelationName)
           ->$entityFn('Zend_TestEntity2', 'table_a.manytoone = table_b.id', $joinedCorrelationName);

        $rsm = $qb->getResultSetMapping();
        $this->assertManyToOneFixture_JoinEntity_ResultSetMapping($rsm);

        $this->assertSqlEquals($expectedSql, $qb);
    }

    static public function dataJoinEntityNonConditions()
    {
        return array(
            array('joinCross', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a CROSS JOIN table_b'),
            array('joinCross', 'a', 'b', 'SELECT a.a_id, a.a_property, a.a_manytoone, b.b_id, b.b_property FROM table_a AS a CROSS JOIN table_b AS b'),
            array('joinCross', 'a', null, 'SELECT a.a_id, a.a_property, a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a AS a CROSS JOIN table_b'),
            array('joinCross', null, 'b', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, b.b_id, b.b_property FROM table_a CROSS JOIN table_b AS b'),
            array('joinNatural', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a NATURAL JOIN table_b'),
            array('joinNatural', 'a', 'b', 'SELECT a.a_id, a.a_property, a.a_manytoone, b.b_id, b.b_property FROM table_a AS a NATURAL JOIN table_b AS b'),
            array('joinNatural', 'a', null, 'SELECT a.a_id, a.a_property, a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a AS a NATURAL JOIN table_b'),
            array('joinNatural', null, 'b', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, b.b_id, b.b_property FROM table_a NATURAL JOIN table_b AS b'),
        );
    }

    /**
     * @dataProvider dataJoinEntityNonConditions
     */
    public function testJoinEntityNonConditions($fn, $rootCorrelationName, $joinedCorrelationName, $expectedSql)
    {
        $entityFn = $fn."Entity";

        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->fromEntity('Zend_TestEntity1', $rootCorrelationName)
           ->$entityFn('Zend_TestEntity2', $joinedCorrelationName);

        $rsm = $qb->getResultSetMapping();
        $this->assertManyToOneFixture_JoinEntity_ResultSetMapping($rsm);

        $this->assertSqlEquals($expectedSql, $qb);
    }

    public function testWithJoined_UsingCorrelationForJoinTable()
    {
        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->fromEntity("Zend_TestEntity1")
            ->joinInner(array("b" => "table_b"), "table_a.manytoone = b.id")
            ->withJoined("Zend_TestEntity2", "b");

        $expectedSql = 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, b.b_id, b.b_property FROM table_a INNER JOIN table_b AS b ON table_a.manytoone = b.id';
        $this->assertSqlEquals($expectedSql, $qb);
    }

    public function testWithJoined_NotUsingCorrelation()
    {
        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->fromEntity("Zend_TestEntity1")
            ->joinInner("table_b", "table_a.manytoone = table_b.id")
            ->withJoined("Zend_TestEntity2");

        $expectedSql = 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone, table_b.b_id, table_b.b_property FROM table_a INNER JOIN table_b ON table_a.manytoone = table_b.id';
        $this->assertSqlEquals($expectedSql, $qb);
    }

    public function testScalarName_IsPlacedInResultSetMapping()
    {
        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->fromEntity("Zend_TestEntity1")
           ->scalar("scalarName", new Zend_Db_Expr("COUNT(foo) AS scalarName"));

        $rsm = $qb->getResultSetMapping();
        $this->assertEquals(array("scalarName"), $rsm->scalarResult);
    }

    public function testScalarValue_IsAddedToColumnResult()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0)) // call to $qo->from() inside $qb->from()
           ->method('columns');
        $qo->expects($this->at(1)) // call to $qo->columns() inside $qb->with()
           ->method('columns');
        $qo->expects($this->at(2))
           ->method('columns')
           ->with($this->equalTo('foo'));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->fromEntity("Zend_TestEntity1")
           ->scalar("scalarName", "foo");
    }

    protected function createManyToOneFixtureQueryBuilder()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = new Zend_Db_Mapper_QueryObject(new Zend_Test_DbAdapter());

        return new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
    }

    static public function dataJoin_IsDelegatedToQueryObject()
    {
        return array(
            array('join', 'table_b', 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a INNER JOIN table_b ON table_a.manytoone = table_b.id'),
            array('join', array('b' => 'table_b'), 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a INNER JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('join', 'table_b', 'table_a.manytoone = table_b.id', 'schemab', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a INNER JOIN schemab.table_b ON table_a.manytoone = table_b.id'),
            array('joinLeft', 'table_b', 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a LEFT JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinLeft', array('b' => 'table_b'), 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a LEFT JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinLeft', 'table_b', 'table_a.manytoone = table_b.id', 'schemab', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a LEFT JOIN schemab.table_b ON table_a.manytoone = table_b.id'),
            array('joinRight', 'table_b', 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a RIGHT JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinRight', array('b' => 'table_b'), 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a RIGHT JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinRight', 'table_b', 'table_a.manytoone = table_b.id', 'schemab', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a RIGHT JOIN schemab.table_b ON table_a.manytoone = table_b.id'),
            array('joinFull', 'table_b', 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a FULL JOIN table_b ON table_a.manytoone = table_b.id'),
            array('joinFull', array('b' => 'table_b'), 'table_a.manytoone = table_b.id', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a FULL JOIN table_b AS b ON table_a.manytoone = table_b.id'),
            array('joinFull', 'table_b', 'table_a.manytoone = table_b.id', 'schemab', 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a FULL JOIN schemab.table_b ON table_a.manytoone = table_b.id'),
            array('joinCross', 'table_b', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a CROSS JOIN table_b'),
            array('joinCross', array('b' => 'table_b'), null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a CROSS JOIN table_b AS b'),
            array('joinCross', 'table_b', 'schemab', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a CROSS JOIN schemab.table_b'),
            array('joinNatural', 'table_b', null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a NATURAL JOIN table_b'),
            array('joinNatural', array('b' => 'table_b'), null, null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a NATURAL JOIN table_b AS b'),
            array('joinNatural', 'table_b', 'schemab', null, 'SELECT table_a.a_id, table_a.a_property, table_a.a_manytoone FROM table_a NATURAL JOIN schemab.table_b'),
        );
    }

    /**
     * @dataProvider dataJoin_IsDelegatedToQueryObject
     */
    public function testJoin_IsDelegatedToQueryObject($fn, $arg1, $arg2, $arg3, $expectedSql)
    {
        $qb = $this->createManyToOneFixtureQueryBuilder();
        $qb->fromEntity("Zend_TestEntity1")
           ->$fn($arg1, $arg2, $arg3);

        $this->assertSqlEquals($expectedSql, $qb);
    }

    /**
     *
     * @param string $expectedSql
     * @param Zend_Db_Mapper_SqlQueryBuilder $qb
     */
    public function assertSqlEquals($expectedSql, Zend_Db_Mapper_SqlQueryBuilder $qb)
    {
        $actualSql = preg_replace('/([\s]{2,})/', ' ', str_replace("\n", " ", $qb->toSql()));
        $this->assertEquals($expectedSql, $actualSql);
    }

    public function assertManyToOneFixture_JoinEntity_ResultSetMapping($rsm)
    {
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertEquals(
            array('a_id' => 'id', 'a_property' => 'property', 'a_manytoone' => 'manytoone'),
            $rsm->entityResult['Zend_TestEntity1']['properties']
        );
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity2']));
        $this->assertEquals(
            array('b_id' => 'id', 'b_property' => 'property'),
            $rsm->entityResult['Zend_TestEntity2']['properties']
        );
        $this->assertEquals(array('Zend_TestEntity1'), $rsm->rootEntity);
        $this->assertEquals(
            array('Zend_TestEntity2' => array('parentEntity' => '', 'parentProperty' => '')),
            $rsm->joinedEntity
        );
    }

    public function testDistinct()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('distinct')
           ->with($this->equalTo(true));
        $qo->expects($this->at(1))
           ->method('distinct')
           ->with($this->equalTo(false));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->distinct(true);
        $qb->distinct(false);
    }

    public function testForUpdate()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('forUpdate')
           ->with($this->equalTo(true));
        $qo->expects($this->at(1))
           ->method('forUpdate')
           ->with($this->equalTo(false));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->forUpdate(true);
        $qb->forUpdate(false);
    }

    public function testOrWhere()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('orWhere')
           ->with($this->equalTo('foo = ?'), $this->equalTo(1));
           
        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->orWhere('foo = ?', 1);
    }

    public function testOrder()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('order')
           ->with($this->equalTo('foo ASC'));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->order("foo ASC");
    }

    public function testGroup()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('group')
           ->with($this->equalTo('foo'));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->group("foo");
    }

    public function testHaving()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('having')
           ->with($this->equalTo('foo = 1'));
        $qo->expects($this->at(1))
           ->method('having')
           ->with($this->equalTo('foo = ?'), $this->equalTo(1));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->having('foo = 1');
        $qb->having('foo = ?', 1);
    }

    public function testOrHaving()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('orHaving')
           ->with($this->equalTo('foo = 1'));
        $qo->expects($this->at(1))
           ->method('orHaving')
           ->with($this->equalTo('foo = ?'), $this->equalTo(1));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->orHaving('foo = 1');
        $qb->orHaving('foo = ?', 1);
    }

    public function testLimit()
    {
        $fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $em = $fixture->createTestEntityManager();

        $qo = $this->getMock('Zend_Db_Mapper_QueryObject', array(), array(), '', false);
        $qo->expects($this->at(0))
           ->method('limit')
           ->with($this->equalTo(10), $this->equalTo(20));

        $qb = new Zend_Db_Mapper_SqlQueryBuilder($em, $qo);
        $qb->limit(10, 20);
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