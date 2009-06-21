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

        $loader = $this->createLoaderMockThatReturnsProccessedResultset($resultWithTwoEntries);
        $mapper = $this->createMapper(null, null, null, $loader);

        $select = $mapper->select();
        $mapper->findOne($select, $this->createEntityManager());
    }

    const TEST_KEY_VALUE = 1;

    public function testloadWithoutIdentityMapMatchHitsDatabaseAdapterForQuery()
    {
        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('query')
           ->will($this->returnValue(new Zend_Entity_DbStatementMock));
           $loader = $this->createLoaderMockThatReturnsProccessedResultset(array(1));

        $mapper = $this->createMapper($db, null, null, $loader);
        $mapper->load(self::TEST_KEY_VALUE, $this->createEntityManager());
    }

    public function testloadWithoutIdentityMapMatchThrowsExceptionIfNotExactlyOneIsFound()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('query')
           ->will($this->returnValue(new Zend_Entity_DbStatementMock));
        $loader = $this->createLoaderMockThatReturnsProccessedResultset(array(1, 2));

        $mapper = $this->createMapper($db, null, null, $loader);
        $mapper->load(self::TEST_KEY_VALUE, $this->createEntityManager());
    }

    public function testloadWithIdentityMapMatchReturnsWithoutHittingDatabase()
    {
        $expectedObject = new stdClass();

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->never())
           ->method('query')
           ->will($this->returnValue(new Zend_Entity_DbStatementMock));
        $identityMap = $this->createIdentityMapMock(0);
        $identityMap->expects($this->once())->method('hasObject')->will($this->returnValue(true));
        $identityMap->expects($this->once())->method('getObject')->will($this->returnValue($expectedObject));

        $entityManager = $this->createEntityManager(null, null, $identityMap);
        $mapper = $this->createMapper($db);

        $actualObject = $mapper->load(self::TEST_KEY_VALUE, $entityManager);

        $this->assertEquals($expectedObject, $actualObject);
    }

    public function testSaveNonLoadedLazyLoadProxyEntityDoesNotDelegateToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->never())->method('save');

        $mapper = $this->createMapper(null, null, null, null, $persister);
        $lazyLoadEntity = $this->createNonLoadedLazyLoadEntity();

        $mapper->save($lazyLoadEntity, $this->createEntityManager());
    }

    public function testSaveNonLazyNonCleanEntityIsDelegatedToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->once())->method('save');
        $entity = $this->getMock('Zend_Entity_Interface');

        $mapper = $this->createMapper(null, null, null, null, $persister);
        $mapper->save($entity, $this->createEntityManager());
    }

    public function testDeleteEntityThatIsNotCleanOrNewIsDelegatedToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->once())->method('delete');
        $entity = $this->getMock('Zend_Entity_Interface');

        $mapper = $this->createMapper(null, null, null, null, $persister);
        $mapper->delete($entity, $this->createEntityManager());
    }
}