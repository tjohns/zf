<?php

class Zend_Entity_DbMapper_SqlQueryTest extends PHPUnit_Framework_TestCase
{
    public function createDbEntityManager()
    {
        $mapper = $this->getMock('Zend_Db_Mapper_Mapper', array(), array(), '', false);
        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $em->expects($this->any())
           ->method('getMapper')
           ->will($this->returnValue($mapper));
        return $em;
    }

    public function testToSql()
    {
        $sqlFixture = "SELECT foo FROM bar WHERE baz = 1";

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $em = $this->createDbEntityManager();
        $query = new Zend_Db_Mapper_SqlQuery($em, $sqlFixture, $rsm);

        $this->assertEquals($sqlFixture, $query->toSql());
    }

    public function testSetMaxResultsThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $em = $this->createDbEntityManager();
        $query = new Zend_Db_Mapper_SqlQuery($em, "sql", $rsm);

        $query->setMaxResults(20);
    }

    public function testSetFirstResultThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $em = $this->createDbEntityManager();
        $query = new Zend_Db_Mapper_SqlQuery($em, "sql", $rsm);

        $query->setFirstResult(20);
    }

    public function testNonDbMapperThrowsConstructorException()
    {
        $this->setExpectedException("Zend_Entity_StorageMissmatchException");

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $em = $this->getMock('Zend_Entity_Manager_Interface');

        $query = new Zend_Db_Mapper_SqlQuery($em, "sql", $rsm);
    }

    public function testExecuteQuery_IsDelegatedToMapperAdapter()
    {
        $fixtureSql = "SELECT foo FROM bar WHERE a = ? AND b = ?";
        $fixtureParams = array(1 => 'a', 2 => 'b');

        $adapterMock = new Zend_Test_DbAdapter();
        $adapterMock->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(array()));

        $mapper = $this->getMock('Zend_Db_Mapper_Mapper', array(), array(), '', false);
        $mapper->expects($this->once())
               ->method('getAdapter')
               ->will($this->returnValue($adapterMock));
        $mapper->expects($this->once())
               ->method('getLoader')
               ->will($this->returnValue($this->getMock('Zend_Db_Mapper_Loader_LoaderAbstract', array('processResultSet'), array(), '', false)));
        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $em->expects($this->exactly(3))
           ->method('getMapper')
           ->will($this->returnValue($mapper));

        $rsm = new Zend_Entity_Query_ResultSetMapping();

        $query = new Zend_Db_Mapper_SqlQuery($em, $fixtureSql, $rsm);
        $query->bindParams($fixtureParams);
        $query->getResultArray();
    }
}