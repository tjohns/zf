<?php

class Zend_Entity_Mapper_NativeQueryTest extends Zend_Entity_TestCase
{
    /**
     *
     * @param <type> $select
     * @param <type> $loader
     * @param <type> $em
     * @return Zend_Entity_Mapper_NativeQuery 
     */
    public function createNativeQuery($select, $loader, $em)
    {
        $mapper = $this->getMock('Zend_Entity_TestMapper', array(), array(), '', false);
        $mapper->expects($this->any())
               ->method('getLoader')
               ->will($this->returnValue($loader));
        $mapper->expects($this->any())
               ->method('select')
               ->will($this->returnValue($select));
        
        return new Zend_Entity_Mapper_NativeQuery($mapper, $em);
    }

    public function testGetResultList_DelegatesToLoader_ProcessResultset()
    {
        $fixtureReturnValue = "foo";

        $em = $this->createTestingEntityManager();
        $select = $this->getSelectMock();
        $loader = $this->getLoaderMock($select);
        $this->addProcessResultsetExpectation($loader, $fixtureReturnValue, $em);

        $query = $this->createNativeQuery($select, $loader, $em);
        $result = $query->getResultList();

        $this->assertEquals($fixtureReturnValue, $result);
    }

    public function addProcessResultsetExpectation($loader, $returnValue, $em)
    {
        $loader->expects($this->once())
               ->method('processResultset')
               ->with($this->isType('array'), $this->equalTo($em))
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
        $select = $this->getSelectMock();
        $loader = $this->getLoaderMock($select);
        $this->addProcessResultsetExpectation($loader, $fixtureReturnValue, $em);

        return $this->createNativeQuery($select, $loader, $em);
    }

    public function testSetMaxResults()
    {
        $select = $this->getSelectMock();
        $select->expects($this->once())
               ->method('limit')
               ->with(30, null);

        $query = $this->createDbSelectQuery($select);
        $q = $query->setMaxResults(30);

        $this->assertSame($query, $q);
    }

    public function testSetFirstResult()
    {
        $select = $this->getSelectMock();
        $select->expects($this->once())
               ->method('limit')
               ->with(null, 30);

        $query = $this->createDbSelectQuery($select);
        $q = $query->setFirstResult(30);

        $this->assertSame($query, $q);
    }

    public function testSetMaxAndFirstResult()
    {
        $select = $this->getSelectMock();
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
        $this->assertNull($query->getParameter('foo'));
    }

    public function testSetParameter()
    {
        $query = $this->createDbSelectQuery();
        $query->setParameter('foo', 'bar');

        $this->assertEquals('bar', $query->getParameter('foo'));
        $this->assertEquals(array('foo' => 'bar'), $query->getParameters());
    }

    public function testSetParameters()
    {
        $query = $this->createDbSelectQuery();
        $query->setParameters(array('foo' => 'bar', 'bar' => 'baz'));

        $this->assertEquals('bar', $query->getParameter('foo'));
        $this->assertEquals('baz', $query->getParameter('bar'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $query->getParameters());
    }

    public function testSetParametersDoesNotResetBindings()
    {
        $query = $this->createDbSelectQuery();
        $query->setParameter('baz', 'foo');
        $query->setParameters(array('foo' => 'bar', 'bar' => 'baz'));

        $this->assertEquals(array('baz' => 'foo', 'foo' => 'bar', 'bar' => 'baz'), $query->getParameters());
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
            $select = $this->getSelectMock();
        }

        $loader = $this->getLoaderMock($select);
        $em = $this->createTestingEntityManager();

        return $this->createNativeQuery($select, $loader, $em);
    }

    public function getLoaderMock()
    {
        return $this->getMock('Zend_Entity_Mapper_Loader_LoaderAbstract', array(), array(), '', false);
    }

    public function getSelectMock()
    {
        $select = $this->getMock('Zend_Entity_Mapper_Select', array(), array(), '', false);
        $select->expects($this->any())
               ->method('query')
               ->will($this->returnValue(new Zend_Test_DbStatement()));
        return $select;
    }
}