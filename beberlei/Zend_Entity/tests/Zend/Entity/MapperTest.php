<?php

class Zend_Entity_MapperTest extends Zend_Entity_TestCase
{
    public function testGetDefinition()
    {
        $entityDefinition = $this->createSampleEntityDefinition();
        $mapper = $this->createMapper(null, $entityDefinition);

        $this->assertEquals($entityDefinition, $mapper->getDefinition());
    }

    public function testSelectInitializesViaLoader()
    {
        $loader = $this->createLoaderMock();
        $loader->expects($this->once())
               ->method('initSelect');
        $mapper = $this->createMapper(null, null, null, $loader);

        $select = $mapper->select();
    }

    public function testFindSelectDelegatesInitColumnsToLoader()
    {
        $loader = $this->createLoaderMock();
        $loader->expects($this->once())
               ->method('initColumns');

        $mapper = $this->createMapper(null, null, null, $loader);
        $select = $mapper->select();
        $mapper->find($select, $this->createEntityManager());
    }

    public function testFindSelectDelegatesResultProcessingToLoader()
    {
        $loader = $this->createLoaderMock();
        $loader->expects($this->once())
               ->method('processResultset');

        $mapper = $this->createMapper(null, null, null, $loader);
        $select = $mapper->select();
        $mapper->find($select, $this->createEntityManager());
    }

    public function testPassedAdapterIsUsedForQuerying()
    {
        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('query')
           ->will($this->returnValue(new Zend_Entity_DbStatementMock));

        $mapper = $this->createMapper($db);
        $select = $mapper->select();
        $mapper->find($select, $this->createEntityManager());
    }

    public function testFindOneThrowsExceptionIfOtherThanOneFound()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $resultWithTwoEntries = array(1, 2);

        $loader = $this->createLoaderMock();
        $loader->expects($this->once())
               ->method('processResultset')
               ->will($this->returnValue($resultWithTwoEntries));
        $mapper = $this->createMapper(null, null, null, $loader);

        $select = $mapper->select();
        $mapper->findOne($select, $this->createEntityManager());
    }

    public function testFindOneEntity()
    {
        $resultWithOneEntry = array(1);

        $loader = $this->createLoaderMock();
        $loader->expects($this->once())
               ->method('processResultset')
               ->will($this->returnValue($resultWithOneEntry));
        $mapper = $this->createMapper(null, null, null, $loader);

        $select = $mapper->select();
        $result = $mapper->findOne($select, $this->createEntityManager());

        $this->assertEquals($resultWithOneEntry[0], $result);
    }
}